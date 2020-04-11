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

use League\CommonMark\ElementRendererInterface;
use League\CommonMark\Extension\Table\TableCell;
use League\CommonMark\Extension\Table\TableCellRenderer;
use League\CommonMark\Extension\Table\TableSection;
use PHPUnit\Framework\TestCase;

final class TableCellRendererTest extends TestCase
{
    public function testRenderWithTableCell()
    {
        $tableCell = new TableCell('', TableCell::TYPE_BODY);
        $tableCell->data['attributes'] = ['class' => 'foo'];

        $elementRenderer = $this->createMock(ElementRendererInterface::class);
        $elementRenderer->method('renderInlines')->willReturn('contents');

        $renderer = new TableCellRenderer();

        $this->assertSame('<td class="foo">contents</td>', (string) $renderer->render($tableCell, $elementRenderer));
    }

    public function testRenderWithTableCellHavingAlignment()
    {
        $tableCell = new TableCell('', TableCell::TYPE_BODY, TableCell::ALIGN_CENTER);
        $tableCell->data['attributes'] = ['class' => 'foo'];

        $elementRenderer = $this->createMock(ElementRendererInterface::class);
        $elementRenderer->method('renderInlines')->willReturn('contents');

        $renderer = new TableCellRenderer();

        $this->assertSame('<td class="foo" align="center">contents</td>', (string) $renderer->render($tableCell, $elementRenderer));
    }

    public function testRenderWithWrongType()
    {
        $this->expectException(\InvalidArgumentException::class);

        (new TableCellRenderer())->render(new TableSection(), $this->createMock(ElementRendererInterface::class));
    }
}
