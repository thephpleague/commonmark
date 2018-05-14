<?php
namespace CommonMarkExt\Tests\Strikethrough;

use CommonMarkExt\Strikethrough\Strikethrough;
use CommonMarkExt\Strikethrough\StrikethroughRenderer;
use CommonMarkExt\Tests\FakeHtmlRenderer;
use League\CommonMark\HtmlElement;
use League\CommonMark\Inline\Renderer\CodeRenderer;

class StrikethroughRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CodeRenderer
     */
    protected $renderer;

    protected function setUp()
    {
        $this->renderer = new StrikethroughRenderer();
    }

    public function testRender()
    {
        $inline = new Strikethrough('reviewed text');
        $inline->data['attributes'] = ['id' => 'some"&amp;id'];
        $fake_renderer = new FakeHtmlRenderer();
        $result = $this->renderer->render($inline, $fake_renderer);
        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('del', $result->getTagName());
        $this->assertContains('reviewed text', $result->getContents(true));
        $this->assertEquals(['id' => 'some&quot;&amp;id'], $result->getAllAttributes());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRenderWithInvalidType()
    {
        $inline = $this->getMockForAbstractClass('League\CommonMark\Inline\Element\AbstractInline');
        $fake_renderer = new FakeHtmlRenderer();
        $this->renderer->render($inline, $fake_renderer);
    }
}
