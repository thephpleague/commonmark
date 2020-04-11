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
use League\CommonMark\Extension\Table\Table;
use League\CommonMark\Extension\Table\TableRenderer;
use League\CommonMark\Extension\Table\TableSection;
use PHPUnit\Framework\TestCase;

final class TableRendererTest extends TestCase
{
    public function testRenderWithTable()
    {
        $tableRow = new Table(function () {});
        $tableRow->data['attributes'] = ['class' => 'striped'];

        $elementRenderer = $this->createMock(ElementRendererInterface::class);
        $elementRenderer->method('renderBlocks')->willReturn('contents');

        $renderer = new TableRenderer();

        $this->assertSame('<table class="striped">contents</table>', (string) $renderer->render($tableRow, $elementRenderer));
    }

    public function testRenderWithWrongType()
    {
        $this->expectException(\InvalidArgumentException::class);

        (new TableRenderer())->render(new TableSection(), $this->createMock(ElementRendererInterface::class));
    }
}
