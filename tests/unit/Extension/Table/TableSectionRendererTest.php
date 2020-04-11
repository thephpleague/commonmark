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
use League\CommonMark\Extension\Table\TableRow;
use League\CommonMark\Extension\Table\TableSection;
use League\CommonMark\Extension\Table\TableSectionRenderer;
use PHPUnit\Framework\TestCase;

final class TableSectionRendererTest extends TestCase
{
    public function testRenderWithTableSection()
    {
        $tableSection = new TableSection(TableSection::TYPE_BODY);
        $tableSection->data['attributes'] = ['class' => 'foo'];
        $tableSection->appendChild(new TableRow());

        $elementRenderer = $this->createMock(ElementRendererInterface::class);
        $elementRenderer->method('renderBlocks')->willReturn('contents');

        $renderer = new TableSectionRenderer();

        $this->assertSame('<tbody class="foo">contents</tbody>', (string) $renderer->render($tableSection, $elementRenderer));
    }

    public function testRenderWithEmptyTableSection()
    {
        $tableSection = new TableSection(TableSection::TYPE_BODY);
        $elementRenderer = $this->createMock(ElementRendererInterface::class);
        $elementRenderer->expects($this->never())->method($this->anything());

        $renderer = new TableSectionRenderer();

        $this->assertSame('', (string) $renderer->render($tableSection, $elementRenderer));
    }

    public function testRenderWithWrongType()
    {
        $this->expectException(\InvalidArgumentException::class);

        (new TableSectionRenderer())->render(new TableCell(), $this->createMock(ElementRendererInterface::class));
    }
}
