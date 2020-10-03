<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Parser\Inline;

final class InlineParserMatch
{
    /** @var string */
    private $regex;

    private function __construct(string $regex)
    {
        $this->regex = $regex;
    }

    /**
     * @internal
     */
    public function getRegex(): string
    {
        return '/' . $this->regex . '/i';
    }

    /**
     * Match the given string (case-insensitive)
     */
    public static function string(string $str): self
    {
        return new self(\preg_quote($str, '/'));
    }

    /**
     * Match any of the given strings (case-insensitive)
     */
    public static function oneOf(string ...$str): self
    {
        return new self(\implode('|', \array_map(static function (string $str): string {
            return \preg_quote($str, '/');
        }, $str)));
    }

    /**
     * Match a partial regular expression without starting/ending delimiters, anchors, or flags
     */
    public static function regex(string $regex): self
    {
        return new self($regex);
    }

    public static function join(self ...$definitions): self
    {
        $regex = '';
        foreach ($definitions as $definition) {
            $regex .= '(' . $definition->regex . ')';
        }

        return new self($regex);
    }
}
