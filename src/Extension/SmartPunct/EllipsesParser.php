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
    public function getMatchDefinition(): InlineParserMatch
    {
        // Stop at the first two periods of each run of periods or of spaced periods; an escape may consume
        // the first one
        return InlineParserMatch::regex('(?<!\.\.)\.(?=\.\.)|(?<!\. \. )\.(?= \. \.)');
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $cursor = $inlineContext->getCursor();
        $first  = $cursor->matchInPlace('/\G(?:\.\.\.|\. \. \.)/');
        if ($first === null) {
            return false;
        }

        // Later ellipses in the same run have no stops of their own, so convert them here too
        [$next, $replacement] = $first === '...' ? ['/\G\.\.\./', '…'] : ['/\G \. \. \./', ' …'];

        $text = '…';
        while ($cursor->matchInPlace($next) !== null) {
            $text .= $replacement;
        }

        $inlineContext->getContainer()->appendChild(new Text($text));

        return true;
    }
}
