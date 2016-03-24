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

namespace League\CommonMark\Block\Parser;

use League\CommonMark\Block\Element\IndentedCode;
use League\CommonMark\Block\Element\Paragraph;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;

class IndentedCodeParser extends AbstractBlockParser
{
    /**
     * @param ContextInterface $context
     * @param Cursor           $cursor
     *
     * @return bool
     */
    public function parse(ContextInterface $context, Cursor $cursor)
    {
        if (!$cursor->isIndented()) {
            return false;
        }

        if ($context->getTip() instanceof Paragraph) {
            return false;
        }

        if ($cursor->isBlank()) {
            return false;
        }

        $cursor->advanceBy(Cursor::INDENT_LEVEL, true);
        $context->addBlock(new IndentedCode());

        return true;
    }
}
