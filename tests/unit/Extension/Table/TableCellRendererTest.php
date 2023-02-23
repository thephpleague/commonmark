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

use League\CommonMark\Exception\InvalidArgumentException;
use League\CommonMark\Extension\Table\TableCell;
use League\CommonMark\Extension\Table\TableCellRenderer;
use League\CommonMark\Extension\Table\TableSection;
use League\CommonMark\Tests\Unit\Renderer\FakeChildNodeRenderer;
use PHPUnit\Framework\TestCase;

final class TableCellRendererTest extends TestCase
{
    public function testRenderWithTableHeader(): void
    {
        $tableCell = new TableCell(TableCell::TYPE_HEADER);
        $tableCell->data->set('attributes/class', 'foo');

        $childRenderer = new FakeChildNodeRenderer();
        $childRenderer->pretendChildrenExist();

        $renderer = new TableCellRenderer();

        $this->assertSame('<th class="foo">::children::</th>', (string) $renderer->render($tableCell, $childRenderer));
    }

    public function testRenderWithTableCell(): void
    {
        $tableCell = new TableCell(TableCell::TYPE_DATA);
        $tableCell->data->set('attributes/class', 'foo');

        $childRenderer = new FakeChildNodeRenderer();
        $childRenderer->pretendChildrenExist();

        $renderer = new TableCellRenderer();

        $this->assertSame('<td class="foo">::children::</td>', (string) $renderer->render($tableCell, $childRenderer));
    }

    public function testRenderWithTableCellHavingAlignment(): void
    {
        $tableCell = new TableCell(TableCell::TYPE_DATA, TableCell::ALIGN_CENTER);
        $tableCell->data->set('attributes/class', 'foo');

        $childRenderer = new FakeChildNodeRenderer();
        $childRenderer->pretendChildrenExist();

        $renderer = new TableCellRenderer();

        $this->assertSame('<td class="foo" align="center">::children::</td>', (string) $renderer->render($tableCell, $childRenderer));
    }

    public function testRenderWithTableCellWithCustomAttributes(): void
    {
        $tableCell = new TableCell(TableCell::TYPE_DATA, TableCell::ALIGN_CENTER);
        $tableCell->data->set('attributes/class', 'foo');

        $childRenderer = new FakeChildNodeRenderer();
        $childRenderer->pretendChildrenExist();

        $renderer = new TableCellRenderer([
            'left' => ['class' => 'foo', 'style' => 'text-align: left'],
            'center' => ['class' => 'bar', 'style' => 'text-align: center'],
            'right' => ['class' => 'baz', 'style' => 'text-align: right'],
        ]);

        $this->assertSame('<td class="foo bar" style="text-align: center">::children::</td>', (string) $renderer->render($tableCell, $childRenderer));
    }

    public function testRenderWithWrongType(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new TableCellRenderer())->render(new TableSection(), new FakeChildNodeRenderer());
    }
}
