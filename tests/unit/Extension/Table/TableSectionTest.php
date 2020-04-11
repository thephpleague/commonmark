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
use League\CommonMark\Extension\Table\TableSection;
use PHPUnit\Framework\TestCase;

final class TableSectionTest extends TestCase
{
    public function testIsHeadAndIsBody()
    {
        $head = new TableSection(TableSection::TYPE_HEAD);
        $this->assertTrue($head->isHead());
        $this->assertFalse($head->isBody());

        $body = new TableSection(TableSection::TYPE_BODY);
        $this->assertFalse($body->isHead());
        $this->assertTrue($body->isBody());
    }

    public function testCanContain()
    {
        $section = new TableSection();

        $this->assertTrue($section->canContain(new TableRow()));
        $this->assertFalse($section->canContain(new TableCell()));
        $this->assertFalse($section->canContain(new Paragraph()));
    }

    public function testIsCode()
    {
        $this->assertFalse((new TableSection())->isCode());
    }

    public function testHandleRemainingContents()
    {
        $context = $this->createMock(ContextInterface::class);
        $context->expects($this->never())->method($this->anything());

        $cursor = $this->createMock(Cursor::class);
        $cursor->expects($this->never())->method($this->anything());

        (new TableSection())->handleRemainingContents($context, $cursor);
    }
}
