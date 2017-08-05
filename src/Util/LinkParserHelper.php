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

        $oldState = $cursor->saveState();
        $openParens = 0;
        while (($c = $cursor->getCharacter()) !== null) {
            if ($c === '\\') {
                $cursor->advance();
                if ($cursor->getCharacter()) {
                    $cursor->advance();
                }
            } elseif ($c === '(') {
                $cursor->advance();
                $openParens++;
            } elseif ($c === ')') {
                if ($openParens < 1) {
                    break;
                } else {
                    $cursor->advance();
                    $openParens--;
                }
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
        $escapedChar = RegexHelper::getInstance()->getPartialRegex(RegexHelper::ESCAPED_CHAR);
        $match = $cursor->match('/^\[(?:[^\\\\\[\]]|' . $escapedChar . '|\\\\)*\]/');
        $length = mb_strlen($match, 'utf-8');

        if ($match === null || $length > 1001 || preg_match('/[^\\\\]\\\\\]$/', $match)) {
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
