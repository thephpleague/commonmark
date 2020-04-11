<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Extension\Table;

use League\CommonMark\Block\Element\Paragraph;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;
use League\CommonMark\Extension\Table\TableCell;
use League\CommonMark\Extension\Table\TableRow;
use PHPUnit\Framework\TestCase;

final class TableCellTest extends TestCase
{
    public function testCanContain()
    {
        $cell = new TableCell();

        $this->assertFalse($cell->canContain(new TableRow()));
        $this->assertFalse($cell->canContain(new TableCell()));
        $this->assertFalse($cell->canContain(new Paragraph()));
    }

    public function testIsCode()
    {
        $this->assertFalse((new TableCell())->isCode());
    }

    public function testMatchesNextLine()
    {
        $cursor = $this->createMock(Cursor::class);
        $cursor->expects($this->never())->method($this->anything());

        (new TableCell())->matchesNextLine($cursor);
    }

    public function testHandleRemainingContents()
    {
        $context = $this->createMock(ContextInterface::class);
        $context->expects($this->never())->method($this->anything());

        $cursor = $this->createMock(Cursor::class);
        $cursor->expects($this->never())->method($this->anything());

        (new TableCell())->handleRemainingContents($context, $cursor);
    }
}
