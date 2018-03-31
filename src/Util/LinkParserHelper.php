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

use League\CommonMark\Cursor;

final class LinkParserHelper
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
        if ($res = $cursor->match(RegexHelper::REGEX_LINK_DESTINATION_BRACES)) {
            // Chop off surrounding <..>:
            return UrlEncoder::unescapeAndEncode(
                RegexHelper::unescape(substr($res, 1, -1))
            );
        }

        $oldState = $cursor->saveState();
        $openParens = 0;
        while (($c = $cursor->getCharacter()) !== null) {
            if ($c === '\\' && RegexHelper::isEscapable($cursor->peek())) {
                $cursor->advanceBy(2);
            } elseif ($c === '(') {
                $cursor->advance();
                $openParens++;
            } elseif ($c === ')') {
                if ($openParens < 1) {
                    break;
                }

                $cursor->advance();
                $openParens--;
            } elseif (preg_match(RegexHelper::REGEX_WHITESPACE_CHAR, $c)) {
                break;
            } else {
                $cursor->advance();
            }
        }

        $newPos = $cursor->getPosition();
        $cursor->restoreState($oldState);

        $cursor->advanceBy($newPos - $cursor->getPosition());

        $res = $cursor->getPreviousText();

        return UrlEncoder::unescapeAndEncode(
            RegexHelper::unescape($res)
        );
    }

    /**
     * @param Cursor $cursor
     *
     * @return int
     */
    public static function parseLinkLabel(Cursor $cursor)
    {
        $match = $cursor->match('/^\[(?:[^\\\\\[\]]|' . RegexHelper::PARTIAL_ESCAPED_CHAR . '|\\\\)*\]/');
        if ($match === null || preg_match('/[^\\\\]\\\\\]$/', $match)) {
            return 0;
        }

        $length = mb_strlen($match, 'utf-8');

        if ($length > 1001) {
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
        if ($title = $cursor->match('/' . RegexHelper::PARTIAL_LINK_TITLE . '/')) {
            // Chop off quotes from title and unescape
            return RegexHelper::unescape(substr($title, 1, -1));
        }
    }
}
