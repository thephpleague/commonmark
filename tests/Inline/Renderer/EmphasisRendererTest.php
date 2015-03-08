<?php

namespace League\CommonMark\Tests\Inline\Renderer;

use League\CommonMark\HtmlElement;
use League\CommonMark\Inline\Element\Emphasis;
use League\CommonMark\Inline\Renderer\EmphasisRenderer;
use League\CommonMark\Tests\FakeHtmlRenderer;

class EmphasisRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EmphasisRenderer
     */
    protected $renderer;

    protected function setUp()
    {
        $this->renderer = new EmphasisRenderer();
    }

    public function testRender()
    {
        $inline = new Emphasis();
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('em', $result->getTagName());
        $this->assertContains('::inlines::', $result->getContents(true));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRenderWithInvalidType()
    {
        $inline = $this->getMockForAbstractClass('League\CommonMark\Inline\Element\AbstractInline');
        $fakeRenderer = new FakeHtmlRenderer();

        $this->renderer->render($inline, $fakeRenderer);
    }
}
