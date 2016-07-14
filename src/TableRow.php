<?php

/*
 * This is part of the webuni/commonmark-table-extension package.
 *
 * (c) Martin Hasoň <martin.hason@gmail.com>
 * (c) Webuni s.r.o. <info@webuni.cz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webuni\CommonMark\TableExtension;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;

class TableRow extends AbstractBlock
{
    public function canContain(AbstractBlock $block)
    {
        return $block instanceof TableCell;
    }

    public function acceptsLines()
    {
        return false;
    }

    public function isCode()
    {
        return false;
    }

    public function matchesNextLine(Cursor $cursor)
    {
        return false;
    }

    public function handleRemainingContents(ContextInterface $context, Cursor $cursor)
    {
    }
}
