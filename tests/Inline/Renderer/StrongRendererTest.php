<?php

namespace League\CommonMark\Tests\Inline\Renderer;

use League\CommonMark\HtmlElement;
use League\CommonMark\Inline\Element\Strong;
use League\CommonMark\Inline\Renderer\StrongRenderer;
use League\CommonMark\Tests\FakeHtmlRenderer;

class StrongRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StrongRenderer
     */
    protected $renderer;

    protected function setUp()
    {
        $this->renderer = new StrongRenderer();
    }

    public function testRender()
    {
        $inline = new Strong();
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('strong', $result->getTagName());
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
