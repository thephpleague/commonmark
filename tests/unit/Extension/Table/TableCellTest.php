<?php

declare(strict_types=1);

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
    public function testDefaultType(): void
    {
        $this->assertSame(TableCell::TYPE_DATA, (new TableCell())->getType());
    }

    public function testTypeConstructorArgument(): void
    {
        $this->assertSame(TableCell::TYPE_HEADER, (new TableCell(TableCell::TYPE_HEADER))->getType());
        $this->assertSame(TableCell::TYPE_DATA, (new TableCell(TableCell::TYPE_DATA))->getType());
    }

    public function testSetType(): void
    {
        $cell = new TableCell(TableCell::TYPE_HEADER);
        $cell->setType(TableCell::TYPE_DATA);

        $this->assertSame(TableCell::TYPE_DATA, $cell->getType());
    }

    public function testDefaultAlign(): void
    {
        $this->assertNull((new TableCell())->getAlign());
    }

    public function testAlignConstructorArgument(): void
    {
        $this->assertNull((new TableCell(TableCell::TYPE_DATA, null))->getAlign());
        $this->assertSame(TableCell::ALIGN_LEFT, (new TableCell(TableCell::TYPE_DATA, TableCell::ALIGN_LEFT))->getAlign());
        $this->assertSame(TableCell::ALIGN_CENTER, (new TableCell(TableCell::TYPE_DATA, TableCell::ALIGN_CENTER))->getAlign());
        $this->assertSame(TableCell::ALIGN_RIGHT, (new TableCell(TableCell::TYPE_DATA, TableCell::ALIGN_RIGHT))->getAlign());
    }

    public function testSetAlign(): void
    {
        $cell = new TableCell();
        $cell->setAlign(TableCell::ALIGN_CENTER);

        $this->assertSame(TableCell::ALIGN_CENTER, $cell->getAlign());
    }
}
