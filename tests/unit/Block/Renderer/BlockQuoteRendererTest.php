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
use League\CommonMark\Block\Element\BlockQuote;
use League\CommonMark\Block\Renderer\BlockQuoteRenderer;
use League\CommonMark\HtmlElement;
use League\CommonMark\Tests\Unit\FakeEmptyHtmlRenderer;
use League\CommonMark\Tests\Unit\FakeHtmlRenderer;
use PHPUnit\Framework\TestCase;

class BlockQuoteRendererTest extends TestCase
{
    /**
     * @var BlockQuoteRenderer
     */
    protected $renderer;

    protected function setUp(): void
    {
        $this->renderer = new BlockQuoteRenderer();
    }

    public function testRenderEmptyBlockQuote()
    {
        $block = new BlockQuote();
        $block->data['attributes'] = ['id' => 'id'];
        $fakeRenderer = new FakeEmptyHtmlRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('blockquote', $result->getTagName());
        $this->assertEmpty($result->getContents(true));
        $this->assertEquals(['id' => 'id'], $result->getAllAttributes());
    }

    public function testRenderBlockQuote()
    {
        $block = new BlockQuote();
        $block->data['attributes'] = ['id' => 'id'];
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('blockquote', $result->getTagName());
        $this->assertStringContainsString('::blocks::', $result->getContents(true));
        $this->assertEquals(['id' => 'id'], $result->getAllAttributes());
    }

    public function testRenderWithInvalidType()
    {
        $this->expectException(\InvalidArgumentException::class);

        $inline = $this->getMockForAbstractClass(BlockElement\AbstractBlock::class);
        $fakeRenderer = new FakeHtmlRenderer();

        $this->renderer->render($inline, $fakeRenderer);
    }
}
