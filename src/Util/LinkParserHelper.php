<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Util;

use League\CommonMark\Parser\Cursor;

/**
 * @psalm-immutable
 */
final class LinkParserHelper
{
    /**
     * Attempt to parse link destination
     *
     * @return string|null The string, or null if no match
     */
    public static function parseLinkDestination(Cursor $cursor): ?string
    {
        if ($cursor->getCurrentCharacter() === '<') {
            return self::parseDestinationBraces($cursor);
        }

        $destination = self::manuallyParseLinkDestination($cursor);
        if ($destination === null) {
            return null;
        }

        return UrlEncoder::unescapeAndEncode(
            RegexHelper::unescape($destination)
        );
    }

    public static function parseLinkLabel(Cursor $cursor): int
    {
        $match = $cursor->matchInPlace('/\G\[(?:[^\\\\\[\]]|\\\\.){0,1000}\]/');
        if ($match === null) {
            return 0;
        }

        $length = \mb_strlen($match, 'UTF-8');

        if ($length > 1001) {
            return 0;
        }

        return $length;
    }

    public static function parsePartialLinkLabel(Cursor $cursor): ?string
    {
        return $cursor->matchInPlace('/\G(?:[^\\\\\[\]]++|\\\\.?)*+/');
    }

    /**
     * Attempt to parse link title (sans quotes)
     *
     * @return string|null The string, or null if no match
     */
    public static function parseLinkTitle(Cursor $cursor): ?string
    {
        if ($title = $cursor->matchInPlace('/\G' . RegexHelper::PARTIAL_LINK_TITLE_UNANCHORED . '/')) {
            // Chop off quotes from title and unescape
            return RegexHelper::unescape(\substr($title, 1, -1));
        }

        return null;
    }

    public static function parsePartialLinkTitle(Cursor $cursor, string $endDelimiter): ?string
    {
        // spec: a parenthesized title may only contain `(` or `)` if backslash-escaped
        $excluded     = \preg_quote($endDelimiter === ')' ? '()' : $endDelimiter, '/');
        $endDelimiter = \preg_quote($endDelimiter, '/');
        $regex        = \sprintf('/(?:%s|[^%s\x00\\\\]++|\\\\)*+(?:%s)?/', RegexHelper::PARTIAL_ESCAPED_CHAR, $excluded, $endDelimiter);
        if (($partialTitle = $cursor->matchInPlace($regex)) === null) {
            return null;
        }

        return RegexHelper::unescape($partialTitle);
    }

    private static function manuallyParseLinkDestination(Cursor $cursor): ?string
    {
        // The destination always ends at the first whitespace or unbalanced ")", so scan the line
        // in place from the cursor rather than materializing the remainder: the cost of finding it
        // should follow the length of the destination, not the length of everything left in the
        // block. A partially-consumed tab needs no special handling - getRemainder() would expand
        // it into leading spaces and the scan would stop on the first one, exactly as it stops on
        // the tab itself here.
        $line       = $cursor->getLine();
        $start      = $cursor->getBytePosition();
        $openParens = 0;
        $len        = \strlen($line) - $start;
        for ($i = 0; $i < $len; $i++) {
            $c = $line[$start + $i];
            if ($c === '\\' && $i + 1 < $len && RegexHelper::isEscapable($line[$start + $i + 1])) {
                $i++;
            } elseif ($c === '(') {
                $openParens++;
                // Limit to 32 nested parens for pathological cases
                if ($openParens > 32) {
                    return null;
                }
            } elseif ($c === ')') {
                if ($openParens < 1) {
                    break;
                }

                $openParens--;
            } elseif (\ord($c) <= 32 && RegexHelper::isWhitespace($c)) {
                break;
            }
        }

        if ($openParens !== 0) {
            return null;
        }

        if ($i === 0 && (! isset($c) || $c !== ')')) {
            return null;
        }

        $destination = \substr($line, $start, $i);
        $cursor->advanceBy(\mb_strlen($destination, 'UTF-8'));

        return $destination;
    }

    private static function parseDestinationBraces(Cursor $cursor): ?string
    {
        // Scanned by hand: under PCRE JIT, a failed regex match here costs time proportional to the
        // rest of the line, which makes many unclosed `<` destinations quadratic
        $line  = $cursor->getLine();
        $start = $cursor->getBytePosition();
        $end   = $start + 1;

        while (true) {
            $end += \strcspn($line, "<>\n\\\x00", $end);
            $char = $line[$end] ?? null;
            if ($char === '>') {
                break;
            }

            // A backslash escapes the next byte unless it's a line break; any other stop means no closing `>`
            if ($char !== '\\' || ! isset($line[$end + 1]) || $line[$end + 1] === "\n") {
                return null;
            }

            $end += 2;
        }

        $destination = \substr($line, $start + 1, $end - $start - 1);
        $cursor->advanceBy(\mb_strlen($destination, 'UTF-8') + 2);

        return UrlEncoder::unescapeAndEncode(RegexHelper::unescape($destination));
    }
}
