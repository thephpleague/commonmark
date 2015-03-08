<?php

namespace League\CommonMark\Tests\Block\Renderer;

use League\CommonMark\Block\Element\BlockQuote;
use League\CommonMark\Block\Renderer\BlockQuoteRenderer;
use League\CommonMark\HtmlElement;
use League\CommonMark\Tests\FakeEmptyHtmlRenderer;
use League\CommonMark\Tests\FakeHtmlRenderer;

class BlockQuoteRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BlockQuoteRenderer
     */
    protected $renderer;

    protected function setUp()
    {
        $this->renderer = new BlockQuoteRenderer();
    }

    public function testRenderEmptyBlockQuote()
    {
        $block = new BlockQuote();
        $fakeRenderer = new FakeEmptyHtmlRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('blockquote', $result->getTagName());
        $this->assertEmpty($result->getContents(true));
    }

    public function testRenderBlockQuote()
    {
        $block = new BlockQuote();
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('blockquote', $result->getTagName());
        $this->assertContains('::blocks::', $result->getContents(true));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRenderWithInvalidType()
    {
        $inline = $this->getMockForAbstractClass('League\CommonMark\Block\Element\AbstractBlock');
        $fakeRenderer = new FakeHtmlRenderer();

        $this->renderer->render($inline, $fakeRenderer);
    }
}
