<?php

namespace League\CommonMark\Tests\Inline\Renderer;

use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Inline\Renderer\TextRenderer;
use League\CommonMark\Tests\FakeHtmlRenderer;

class TextRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TextRenderer
     */
    protected $renderer;

    protected function setUp()
    {
        $this->renderer = new TextRenderer();
    }

    public function testRender()
    {
        $inline = new Text('foo bar');
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertInternalType('string', $result);
        $this->assertContains('foo bar', $result);
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
