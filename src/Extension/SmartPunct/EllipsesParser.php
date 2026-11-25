<?php

declare(strict_types=1);

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

namespace League\CommonMark\Extension\SmartPunct;

use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;

final class EllipsesParser implements InlineParserInterface
{
    private const ELLIPSIS = '(?:\.\.\.|\. \. \.)';

    public function getMatchDefinition(): InlineParserMatch
    {
        // Match whole ellipses like a plain scan would, except that an escaped period (after one or three
        // backslashes) only matches itself: the escape consumes it, and an ellipsis may still start at the next period
        return InlineParserMatch::regex('(?<=(?<!\\\\)\\\\|(?<!\\\\)\\\\\\\\\\\\)\.(?=\.\.| \. \.)|\.\.\.|\. \. \.');
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $cursor = $inlineContext->getCursor();
        if ($cursor->matchInPlace('/\G' . self::ELLIPSIS . '/') === null) {
            return false;
        }

        $text = '…';

        // Reaching a lone escaped period means something other than an escape consumed the backslash, so the
        // later matches in this run of periods and spaces are misaligned; convert the rest of the run here instead,
        // finding each ellipsis the same way a plain left-to-right scan would
        if ($inlineContext->getFullMatch() === '.') {
            while (($gap = $cursor->matchInPlace('/\G[. ]*?(?=' . self::ELLIPSIS . ')/')) !== null) {
                $cursor->matchInPlace('/\G' . self::ELLIPSIS . '/');
                $text .= $gap . '…';
            }
        }

        $inlineContext->getContainer()->appendChild(new Text($text));

        return true;
    }
}
