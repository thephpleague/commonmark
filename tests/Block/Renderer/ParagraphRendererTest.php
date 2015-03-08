<?php

namespace League\CommonMark\Tests\Block\Renderer;

use League\CommonMark\Block\Element\Paragraph;
use League\CommonMark\Block\Renderer\ParagraphRenderer;
use League\CommonMark\HtmlElement;
use League\CommonMark\Tests\FakeHtmlRenderer;

class ParagraphRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ParagraphRenderer
     */
    protected $renderer;

    protected function setUp()
    {
        $this->renderer = new ParagraphRenderer();
    }

    public function testRender()
    {
        $block = new Paragraph();
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('p', $result->getTagName());
        $this->assertContains('::inlines::', $result->getContents(true));
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
