<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Util;

use League\CommonMark\Cursor;

class LinkParserHelper
{
    /**
     * Attempt to parse link destination
     *
     * @param Cursor $cursor
     *
     * @return null|string The string, or null if no match
     */
    public static function parseLinkDestination(Cursor $cursor)
    {
        if ($res = $cursor->match(RegexHelper::getInstance()->getLinkDestinationBracesRegex())) {
            // Chop off surrounding <..>:
            return UrlEncoder::unescapeAndEncode(
                RegexHelper::unescape(substr($res, 1, strlen($res) - 2))
            );
        }

        $res = $cursor->match(RegexHelper::getInstance()->getLinkDestinationRegex());
        if ($res !== null) {
            return UrlEncoder::unescapeAndEncode(
                RegexHelper::unescape($res)
            );
        }
    }

    /**
     * @param Cursor $cursor
     *
     * @return int
     */
    public static function parseLinkLabel(Cursor $cursor)
    {
        $escapedChar = RegexHelper::getInstance()->getPartialRegex(RegexHelper::ESCAPED_CHAR);
        $match = $cursor->match('/^\[(?:[^\\\\\[\]]|' . $escapedChar . '|\\\\)*\]/');
        $length = mb_strlen($match, 'utf-8');

        if ($match === null || $length > 1001) {
            return 0;
        }

        return $length;
    }

    /**
     * Attempt to parse link title (sans quotes)
     *
     * @param Cursor $cursor
     *
     * @return null|string The string, or null if no match
     */
    public static function parseLinkTitle(Cursor $cursor)
    {
        if ($title = $cursor->match(RegexHelper::getInstance()->getLinkTitleRegex())) {
            // Chop off quotes from title and unescape
            return RegexHelper::unescape(substr($title, 1, strlen($title) - 2));
        }
    }
}
