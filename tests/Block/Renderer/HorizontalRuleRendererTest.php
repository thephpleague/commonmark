<?php

namespace League\CommonMark\Tests\Block\Renderer;

use League\CommonMark\Block\Element\HorizontalRule;
use League\CommonMark\Block\Renderer\HorizontalRuleRenderer;
use League\CommonMark\HtmlElement;
use League\CommonMark\Tests\FakeHtmlRenderer;

class HorizontalRuleRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HorizontalRuleRenderer
     */
    protected $renderer;

    protected function setUp()
    {
        $this->renderer = new HorizontalRuleRenderer();
    }

    public function testRender()
    {
        $block = new HorizontalRule();
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('hr', $result->getTagName());
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
