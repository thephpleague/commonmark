<?php

/*
 * This file is part of the league/commonmark-extras package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Ext\SmartPunct;

use League\CommonMark\Delimiter\Delimiter;
use League\CommonMark\Delimiter\DelimiterStack;
use League\CommonMark\Inline\Processor\InlineProcessorInterface;

class QuoteProcessor implements InlineProcessorInterface
{
    public function processInlines(DelimiterStack $delimiterStack, Delimiter $stackBottom = null)
    {
        $callback = function (Delimiter $opener, Delimiter $closer) {
            // Open quote
            $openerInline = $opener->getInlineNode();
            $openerInline->setContent(
                $openerInline->getContent() === '“' ? '“' : '‘'
            );

            // Close quote
            $closerInline = $closer->getInlineNode();
            $closerInline->setContent(
                $closerInline->getContent() === '“' ? '”' : '’'
            );

            return $closer->getNext();
        };

        // Process the emphasis characters
        $delimiterStack->iterateByCharacters(['“', '’'], $callback, $stackBottom);
    }
}
