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

namespace League\CommonMark\Inline\Processor;

use League\CommonMark\Delimiter\Delimiter;
use League\CommonMark\Delimiter\DelimiterStack;
use League\CommonMark\Inline\Element\Emphasis;
use League\CommonMark\Inline\Element\Strong;
use League\CommonMark\Inline\Element\Text;

class EmphasisProcessor implements InlineProcessorInterface
{
    public function processInlines(DelimiterStack $delimiterStack, Delimiter $stackBottom = null)
    {
        $callback = function (Delimiter $opener, Delimiter $closer, DelimiterStack $stack) {
            // Calculate actual number of delimiters used from this closer
            $useDelims = ($closer->getNumDelims() >= 2 && $opener->getNumDelims() >= 2) ? 2 : 1;
            /** @var Text $openerInline */
            $openerInline = $opener->getInlineNode();
            /** @var Text $closerInline */
            $closerInline = $closer->getInlineNode();

            // Remove used delimiters from stack elts and inlines
            $opener->setNumDelims($opener->getNumDelims() - $useDelims);
            $closer->setNumDelims($closer->getNumDelims() - $useDelims);
            $openerInline->setContent(substr($openerInline->getContent(), 0, -$useDelims));
            $closerInline->setContent(substr($closerInline->getContent(), 0, -$useDelims));

            // Build contents for new emph element
            if ($useDelims === 1 && $openerInline->data['emphasis_config']->getConfig('enable_em')) {
                $emph = new Emphasis();
            } elseif ($useDelims > 1 && $openerInline->data['emphasis_config']->getConfig('enable_strong')) {
                $emph = new Strong();
            } else {
                return $closer->getNext();
            }

            $openerInline->insertAfter($emph);
            while (($node = $emph->next()) !== $closerInline) {
                $emph->appendChild($node);
            }

            // Remove elts btw opener and closer in delimiters stack
            $tempStack = $closer->getPrevious();
            while ($tempStack !== null && $tempStack !== $opener) {
                $nextStack = $tempStack->getPrevious();
                $stack->removeDelimiter($tempStack);
                $tempStack = $nextStack;
            }
            // If opener has 0 delims, remove it and the inline
            if ($opener->getNumDelims() === 0) {
                $openerInline->detach();
                $stack->removeDelimiter($opener);
            }
            if ($closer->getNumDelims() === 0) {
                $closerInline->detach();
                $tempStack = $closer->getNext();
                $stack->removeDelimiter($closer);

                return $tempStack;
            }

            return $closer;
        };

        // Process the emphasis characters
        $delimiterStack->iterateByCharacters(['_', '*'], $callback, $stackBottom);
    }
}
