<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Davey Shafik <me@daveyshafik.com
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmarkjs)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Markua\Block\Parser;

use League\CommonMark\Block\Parser\AbstractBlockParser;
use League\CommonMark\Markua\Block\Element\Aside;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;

class AsideParser extends AbstractBlockParser
{
    /**
     * @param ContextInterface $context
     * @param Cursor $cursor
     *
     * @return bool
     */
    public function parse(ContextInterface $context, Cursor $cursor)
    {
        if ($cursor->getFirstNonSpaceCharacter() !== 'A' || $cursor->getCharacter($cursor->getFirstNonSpacePosition() + 1) !== '>') {
            return false;
        }

        $cursor->advanceToFirstNonSpace();
        $cursor->advance();
        if ($cursor->getCharacter() === '>') {
            $cursor->advance();
            if ($cursor->getCharacter() === ' ') {
                $cursor->advance();
            }
        }

        $context->addBlock(new Aside());

        return true;
    }
}
