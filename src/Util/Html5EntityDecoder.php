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

/**
 * @psalm-immutable
 */
final class Html5EntityDecoder
{
    /**
     * @psalm-pure
     */
    public static function decode(string $entity): string
    {
        if (\substr($entity, -1) !== ';') {
            return $entity;
        }

        if (\substr($entity, 0, 2) === '&#') {
            if (\strtolower(\substr($entity, 2, 1)) === 'x') {
                return self::fromHex(\substr($entity, 3, -1));
            }

            return self::fromDecimal(\substr($entity, 2, -1));
        }

        return \html_entity_decode($entity, \ENT_QUOTES | \ENT_HTML5, 'UTF-8');
    }

    /**
     * @param mixed $number
     *
     * @psalm-pure
     */
    private static function fromDecimal($number): string
    {
        // Per the spec, NULL, surrogates and anything beyond U+10FFFF become U+FFFD.
        // Check the upper bound first, as casting a huge float to int would wrap around.
        if ($number > 0x10FFFF) {
            return "\u{FFFD}";
        }

        $number = (int) $number;
        if ($number <= 0 || ($number >= 0xD800 && $number <= 0xDFFF)) {
            return "\u{FFFD}";
        }

        $char = \mb_chr($number, 'UTF-8');

        return $char === false ? "\u{FFFD}" : $char;
    }

    /**
     * @psalm-pure
     */
    private static function fromHex(string $hexChars): string
    {
        return self::fromDecimal(\hexdec($hexChars));
    }
}
