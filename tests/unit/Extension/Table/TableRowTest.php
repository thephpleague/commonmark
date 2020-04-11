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

use League\CommonMark\Cursor;
use League\CommonMark\Extension\Table\TableCell;
use League\CommonMark\Extension\Table\TableRow;
use PHPUnit\Framework\TestCase;

final class TableRowTest extends TestCase
{
    public function testCanContain()
    {
        $row = new TableRow();

        $this->assertTrue($row->canContain(new TableCell()));
        $this->assertFalse($row->canContain(new TableRow()));
    }

    public function testIsCode()
    {
        $this->assertFalse((new TableRow())->isCode());
    }

    public function testMatchesNextLine()
    {
        $this->assertFalse((new TableRow())->matchesNextLine(new Cursor('')));
    }
}
