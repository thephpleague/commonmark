<?php

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

namespace League\CommonMark\Tests\Unit\Block\Renderer;

use League\CommonMark\Block\Element as BlockElement;
use League\CommonMark\Block\Element\ListData;
use League\CommonMark\Block\Element\ListItem;
use League\CommonMark\Block\Renderer\ListItemRenderer;
use League\CommonMark\HtmlElement;
use League\CommonMark\Tests\Unit\FakeHtmlRenderer;
use PHPUnit\Framework\TestCase;

class ListItemRendererTest extends TestCase
{
    /**
     * @var ListItemRenderer
     */
    protected $renderer;

    protected function setUp(): void
    {
        $this->renderer = new ListItemRenderer();
    }

    public function testRenderUnorderedList()
    {
        $block = new ListItem(new ListData());
        $block->data['attributes'] = ['id' => 'foo'];
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('li', $result->getTagName());
        $this->assertEquals('<li id="foo">::blocks::</li>', $result->__toString());
    }

    public function testRenderWithInvalidType()
    {
        $this->expectException(\InvalidArgumentException::class);

        $inline = $this->getMockForAbstractClass(BlockElement\AbstractBlock::class);
        $fakeRenderer = new FakeHtmlRenderer();

        $this->renderer->render($inline, $fakeRenderer);
    }
}
