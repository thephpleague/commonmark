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
use League\CommonMark\Extension\Table\TableRow;
use League\CommonMark\Extension\Table\TableSection;
use League\CommonMark\Extension\Table\TableSectionRenderer;
use League\CommonMark\Tests\Unit\Renderer\FakeChildNodeRenderer;
use PHPUnit\Framework\TestCase;

final class TableSectionRendererTest extends TestCase
{
    public function testRenderWithTableSection(): void
    {
        $tableSection = new TableSection(TableSection::TYPE_BODY);
        $tableSection->data->set('attributes/class', 'foo');
        $tableSection->appendChild(new TableRow());

        $childRenderer = new FakeChildNodeRenderer();
        $childRenderer->pretendChildrenExist();

        $renderer = new TableSectionRenderer();

        $this->assertSame('<tbody class="foo">::children::</tbody>', (string) $renderer->render($tableSection, $childRenderer));
    }

    public function testRenderWithEmptyTableSection(): void
    {
        $tableSection  = new TableSection(TableSection::TYPE_BODY);
        $childRenderer = new FakeChildNodeRenderer();

        $renderer = new TableSectionRenderer();

        $this->assertSame('', (string) $renderer->render($tableSection, $childRenderer));
    }

    public function testRenderWithWrongType(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new TableSectionRenderer())->render(new TableCell(), new FakeChildNodeRenderer());
    }
}
