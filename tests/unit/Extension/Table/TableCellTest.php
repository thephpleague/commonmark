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

use League\CommonMark\Extension\Table\TableCell;
use PHPUnit\Framework\TestCase;

final class TableCellTest extends TestCase
{
    public function testDefaultType()
    {
        $this->assertSame(TableCell::TYPE_BODY, (new TableCell())->getType());
    }

    public function testTypeConstructorArgument()
    {
        $this->assertSame(TableCell::TYPE_HEAD, (new TableCell(TableCell::TYPE_HEAD))->getType());
        $this->assertSame(TableCell::TYPE_BODY, (new TableCell(TableCell::TYPE_BODY))->getType());
    }

    public function testSetType()
    {
        $cell = new TableCell(TableCell::TYPE_HEAD);
        $cell->setType(TableCell::TYPE_BODY);

        $this->assertSame(TableCell::TYPE_BODY, $cell->getType());
    }

    public function testDefaultAlign()
    {
        $this->assertNull((new TableCell())->getAlign());
    }

    public function testAlignConstructorArgument()
    {
        $this->assertNull((new TableCell(TableCell::TYPE_BODY, null))->getAlign());
        $this->assertSame(TableCell::ALIGN_LEFT, (new TableCell(TableCell::TYPE_BODY, TableCell::ALIGN_LEFT))->getAlign());
        $this->assertSame(TableCell::ALIGN_CENTER, (new TableCell(TableCell::TYPE_BODY, TableCell::ALIGN_CENTER))->getAlign());
        $this->assertSame(TableCell::ALIGN_RIGHT, (new TableCell(TableCell::TYPE_BODY, TableCell::ALIGN_RIGHT))->getAlign());
    }

    public function testSetAlign()
    {
        $cell = new TableCell();
        $cell->setAlign(TableCell::ALIGN_CENTER);

        $this->assertSame(TableCell::ALIGN_CENTER, $cell->getAlign());
    }
}
