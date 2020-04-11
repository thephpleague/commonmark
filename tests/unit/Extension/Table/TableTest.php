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

use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;
use League\CommonMark\Extension\Table\Table;
use League\CommonMark\Extension\Table\TableCell;
use League\CommonMark\Extension\Table\TableSection;
use PHPUnit\Framework\TestCase;

final class TableTest extends TestCase
{
    public function testCanContain()
    {
        $table = new Table(function () {});

        $this->assertTrue($table->canContain(new TableSection()));
        $this->assertFalse($table->canContain(new TableCell()));
    }

    public function testIsCode()
    {
        $this->assertFalse((new Table(function () {}))->isCode());
    }

    public function testGetHeadAndBody()
    {
        $table = new Table(function () {});

        $this->assertNotNull($table->getHead());
        $this->assertNotNull($table->getBody());
    }

    public function testMatchesNextLine()
    {
        $table = new Table(function () { return false; });

        $this->assertFalse($table->matchesNextLine(new Cursor('')));
    }

    public function testHandleRemainingContents()
    {
        $context = $this->createMock(ContextInterface::class);
        $context->expects($this->never())->method($this->anything());

        $cursor = $this->createMock(Cursor::class);
        $cursor->expects($this->never())->method($this->anything());

        (new Table(function () {}))->handleRemainingContents($context, $cursor);
    }
}
