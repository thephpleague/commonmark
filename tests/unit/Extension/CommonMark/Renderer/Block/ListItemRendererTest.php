<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Extension\CommonMark\Renderer\Block;

use League\CommonMark\Exception\InvalidArgumentException;
use League\CommonMark\Extension\CommonMark\Node\Block\ListData;
use League\CommonMark\Extension\CommonMark\Node\Block\ListItem;
use League\CommonMark\Extension\CommonMark\Renderer\Block\ListItemRenderer;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Tests\Unit\Renderer\FakeChildNodeRenderer;
use League\CommonMark\Util\HtmlElement;
use PHPUnit\Framework\TestCase;

final class ListItemRendererTest extends TestCase
{
    private ListItemRenderer $renderer;

    protected function setUp(): void
    {
        $this->renderer = new ListItemRenderer();
    }

    public function testRenderUnorderedList(): void
    {
        $block = new ListItem(new ListData());
        $block->data->set('attributes/id', 'foo');
        $fakeRenderer = new FakeChildNodeRenderer();
        $fakeRenderer->pretendChildrenExist();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('li', $result->getTagName());
        $this->assertEquals('<li id="foo">::children::</li>', $result->__toString());
    }

    public function testRenderWithInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $inline       = $this->getMockForAbstractClass(AbstractBlock::class);
        $fakeRenderer = new FakeChildNodeRenderer();

        $this->renderer->render($inline, $fakeRenderer);
    }
}
