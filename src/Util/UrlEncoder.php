<?php

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

final class UrlEncoder
{
    protected static $dontEncode = [
        '%21' => '!',
        '%23' => '#',
        '%24' => '$',
        '%26' => '&',
        '%27' => '\'',
        '%28' => '(',
        '%29' => ')',
        '%2A' => '*',
        '%2B' => '+',
        '%2C' => ',',
        '%2D' => '-',
        '%2E' => '.',
        '%2F' => '/',
        '%3A' => ':',
        '%3B' => ';',
        '%3D' => '=',
        '%3F' => '?',
        '%40' => '@',
        '%5F' => '_',
        '%7E' => '~',
    ];

    /**
     * @param string $uri
     *
     * @return string
     */
    public static function unescapeAndEncode($uri)
    {
        $decoded = html_entity_decode($uri);

        return self::encode(self::decode($decoded));
    }

    /**
     * Decode a percent-encoded URI
     *
     * @param string $uri
     *
     * @return string
     */
    private static function decode($uri)
    {
        return preg_replace_callback('/%([0-9a-f]{2})/iu', function ($matches) {
            // Convert percent-encoded codes to uppercase
            $upper = strtoupper($matches[0]);
            // Keep excluded characters as-is
            if (array_key_exists($upper, self::$dontEncode)) {
                return $upper;
            }

            // Otherwise, return the character for this codepoint
            return chr(hexdec($matches[1]));
        }, $uri);
    }

    /**
     * Encode a URI, preserving already-encoded and excluded characters
     *
     * @param string $uri
     *
     * @return string
     */
    private static function encode($uri)
    {
        return preg_replace_callback('/(%[0-9a-f]{2})|./iu', function ($matches) {
            // Keep already-encoded characters as-is
            if (count($matches) > 1) {
                return $matches[0];
            }

            // Keep excluded characters as-is
            if (in_array($matches[0], self::$dontEncode)) {
                return $matches[0];
            }

            // Otherwise, encode the character
            return rawurlencode($matches[0]);
        }, $uri);
    }
}
