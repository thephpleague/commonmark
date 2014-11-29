<?php

namespace ColinODell\CommonMark\Util;

class UrlEncoder
{
    protected static $dontEncode = array(
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
        '%7E' => '~'
    );

    /**
     * @param string $uri
     *
     * @return string
     */
    public static function unescapeAndEncode($uri)
    {
        $decoded = html_entity_decode($uri);

        return strtr(rawurlencode(rawurldecode($decoded)), self::$dontEncode);
    }
}
