<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmarkjs)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Inline\Parser;

use League\CommonMark\ContextInterface;
use League\CommonMark\InlineParserContext;
use League\CommonMark\Inline\Element\Newline;

class NewlineParser extends AbstractInlineParser
{
    /**
     * @return string[]
     */
    public function getCharacters()
    {
        return array("\n", ' ');
    }

    /**
     * @param ContextInterface $context
     * @param InlineParserContext $inlineContext
     *
     * @return bool
     */
    public function parse(ContextInterface $context, InlineParserContext $inlineContext)
    {
        if ($m = $inlineContext->getCursor()->match('/^ *\n/')) {
            if (strlen($m) > 2) {
                $inlineContext->getInlines()->add(new Newline(Newline::HARDBREAK));

                return true;
            } elseif (strlen($m) > 0) {
                $inlineContext->getInlines()->add(new Newline(Newline::SOFTBREAK));

                return true;
            }
        }

        return false;
    }
}
