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
use League\CommonMark\Extension\Table\Table;
use League\CommonMark\Extension\Table\TableRenderer;
use League\CommonMark\Extension\Table\TableSection;
use League\CommonMark\Tests\Unit\Renderer\FakeChildNodeRenderer;
use PHPUnit\Framework\TestCase;

final class TableRendererTest extends TestCase
{
    public function testRenderWithTable(): void
    {
        $tableRow = new Table();
        $tableRow->data->set('attributes/class', 'striped');

        $childRenderer = new FakeChildNodeRenderer();
        $childRenderer->pretendChildrenExist();

        $renderer = new TableRenderer();

        $this->assertSame('<table class="striped">::children::</table>', (string) $renderer->render($tableRow, $childRenderer));
    }

    public function testRenderWithWrongType(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new TableRenderer())->render(new TableSection(), new FakeChildNodeRenderer());
    }
}
