<?php

namespace League\CommonMark\Tests\Block\Renderer;

use League\CommonMark\Block\Element\IndentedCode;
use League\CommonMark\Block\Renderer\IndentedCodeRenderer;
use League\CommonMark\HtmlElement;
use League\CommonMark\Tests\FakeHtmlRenderer;

class IndentedCodeRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IndentedCodeRenderer
     */
    protected $renderer;

    protected function setUp()
    {
        $this->renderer = new IndentedCodeRenderer();
    }

    public function testRender()
    {
        $block = new IndentedCode();
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('pre', $result->getTagName());

        $code = $result->getContents(false);
        $this->assertTrue($code instanceof HtmlElement);
        $this->assertEquals('code', $code->getTagName());
        $this->assertNull($code->getAttribute('class'));
        $this->assertContains('::escape::', $code->getContents(true));
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
