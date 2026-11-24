<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 * (c) 2015 Martin Hasoň <martin.hason@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\CommonMark\Extension\Attributes\Util;

use League\CommonMark\Node\Node;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Util\RegexHelper;

/**
 * @internal
 */
final class AttributesHelper
{
    /**
     * Each token can only be split from its neighbors one way, and the repeated parts are possessive,
     * so a failed match can't backtrack exponentially. An unquoted value may contain '}' only if
     * another '}' follows in the same run; the last one closes the list.
     */
    private const SINGLE_ATTRIBUTE = '\s*+(?:[.]-?[_a-z][^\s.}]*+|[#][^\s}]++|' . RegexHelper::PARTIAL_ATTRIBUTENAME . '\s*+=\s*+(?:(?:[^"\'=<>`\x00-\x20}]++|}(?=[^"\'=<>`\x00-\x20}]*+}))++|\'[^\']*+\'|"[^"]*+"))\s*+';
    private const ATTRIBUTE_LIST   = '/^{:?+(?:' . self::SINGLE_ATTRIBUTE . ')++}/i';

    /**
     * Each '{' is a potential list, so the text examined for one must be bounded; otherwise a
     * line with many of them costs quadratic time.
     */
    private const MAX_ATTRIBUTE_LIST_BYTES = 8192;

    /**
     * PCRE's `\s` matches the form feed that PHP's default trim charlist omits, so the
     * separators SINGLE_ATTRIBUTE accepts must be trimmed with this list instead - otherwise
     * that byte survives inside an attribute name, where a browser reads it as a separator.
     */
    private const WHITESPACE = " \t\n\r\0\x0B\x0C";

    /**
     * @return array<string, mixed>
     */
    public static function parseAttributes(Cursor $cursor): array
    {
        $state = $cursor->saveState();
        $cursor->advanceToNextNonSpaceOrNewline();

        // Quick check to see if we might have attributes
        if ($cursor->getCharacter() !== '{') {
            $cursor->restoreState($state);

            return [];
        }

        // Match the whole list up front, so nothing is consumed unless it's valid. The cursor is on
        // the '{', so no partially-consumed tab can be pending.
        $window = \substr($cursor->getLine(), $cursor->getBytePosition(), self::MAX_ATTRIBUTE_LIST_BYTES);
        if (\preg_match(self::ATTRIBUTE_LIST, $window, $matches) !== 1) {
            $cursor->restoreState($state);

            return [];
        }

        $attributeExpression = $matches[0];
        $cursor->advanceBy(\mb_strlen($attributeExpression, 'UTF-8'));

        // Trim the leading '{' or '{:'; the closing '}' stays, since SINGLE_ATTRIBUTE needs it to
        // decide whether a '}' belongs to an unquoted value
        $attributeExpression = \ltrim(\substr($attributeExpression, 1), ':');
        $attributeCursor     = new Cursor($attributeExpression);

        /** @var array<string, mixed> $attributes */
        $attributes = [];
        while ($attribute = \trim((string) $attributeCursor->matchInPlace('/\G' . self::SINGLE_ATTRIBUTE . '/i'), self::WHITESPACE)) {
            if ($attribute[0] === '#') {
                $attributes['id'] = \substr($attribute, 1);

                continue;
            }

            if ($attribute[0] === '.') {
                $attributes['class'][] = \substr($attribute, 1);

                continue;
            }

            /** @psalm-suppress PossiblyUndefinedArrayOffset */
            [$name, $value] = \explode('=', $attribute, 2);

            $name      = \trim($name, self::WHITESPACE);
            $lowerName = \strtolower($name);

            // A bare `true` makes a boolean attribute, except where other code expects a string
            if ($value === 'true' && $lowerName !== 'class' && $lowerName !== 'id') {
                $attributes[$name] = true;
                continue;
            }

            $value = \trim($value, self::WHITESPACE);
            $first = $value[0];
            $last  = \substr($value, -1);
            if (\strlen($value) > 1 && (($first === '"' && $last === '"') || ($first === "'" && $last === "'"))) {
                $value = \trim(\substr($value, 1, -1), self::WHITESPACE);
            }

            if ($lowerName === 'class') {
                foreach (\array_filter(\explode(' ', $value)) as $class) {
                    $attributes['class'][] = $class;
                }
            } else {
                $attributes[$name] = $value;
            }
        }

        if (isset($attributes['class'])) {
            $attributes['class'] = \implode(' ', (array) $attributes['class']);
        }

        return $attributes;
    }

    /**
     * @param Node|array<string, mixed> $attributes1
     * @param Node|array<string, mixed> $attributes2
     *
     * @return array<string, mixed>
     */
    public static function mergeAttributes($attributes1, $attributes2): array
    {
        $attributes = [];
        foreach ([$attributes1, $attributes2] as $arg) {
            if ($arg instanceof Node) {
                $arg = $arg->data->get('attributes');
            }

            /** @var array<string, mixed> $arg */
            $arg = (array) $arg;
            if (isset($arg['class'])) {
                foreach (self::classList($arg['class']) as $class) {
                    $attributes['class'][] = $class;
                }

                unset($arg['class']);
            }

            $attributes = \array_merge($attributes, $arg);
        }

        if (isset($attributes['class'])) {
            $attributes['class'] = \implode(' ', $attributes['class']);
        }

        return $attributes;
    }

    /**
     * Split a `class` attribute value into the individual classes it contributes to a merge
     *
     * @param mixed $class
     *
     * @return list<string>
     */
    public static function classList($class): array
    {
        if (\is_string($class)) {
            return \array_values(\array_filter(\explode(' ', \trim($class))));
        }

        return \array_values((array) $class);
    }

    /**
     * @param array<string, mixed> $attributes
     * @param list<string>         $allowList
     *
     * @return array<string, mixed>
     */
    public static function filterAttributes(array $attributes, array $allowList, bool $allowUnsafeLinks): array
    {
        $allowList = \array_fill_keys($allowList, true);

        foreach ($attributes as $name => $value) {
            // The checks below compare against literal names, and the renderer emits names
            // without escaping them, so anything that isn't a well-formed attribute name
            // would slip past both
            if (\preg_match('/^' . RegexHelper::PARTIAL_ATTRIBUTENAME . '$/i', (string) $name) !== 1) {
                unset($attributes[$name]);
                continue;
            }

            $attrNameLower = \strtolower((string) $name);

            // Remove any unsafe links
            if (! $allowUnsafeLinks && ($attrNameLower === 'href' || $attrNameLower === 'src') && \is_string($value) && RegexHelper::isLinkPotentiallyUnsafe($value)) {
                unset($attributes[$name]);
                continue;
            }

            // No allowlist?
            if ($allowList === []) {
                // Just remove JS event handlers
                if (\str_starts_with($attrNameLower, 'on')) {
                    unset($attributes[$name]);
                }

                continue;
            }

            // Remove any attributes not in that allowlist (case-sensitive)
            if (! isset($allowList[$name])) {
                unset($attributes[$name]);
            }
        }

        return $attributes;
    }
}
