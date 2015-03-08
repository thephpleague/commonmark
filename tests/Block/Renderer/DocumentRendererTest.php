<?php

namespace League\CommonMark\Tests\Block\Renderer;

use League\CommonMark\Block\Element\Document;
use League\CommonMark\Block\Renderer\DocumentRenderer;
use League\CommonMark\Tests\FakeEmptyHtmlRenderer;
use League\CommonMark\Tests\FakeHtmlRenderer;

class DocumentRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DocumentRenderer
     */
    protected $renderer;

    protected function setUp()
    {
        $this->renderer = new DocumentRenderer();
    }

    public function testRenderEmptyDocument()
    {
        $block = new Document();
        $fakeRenderer = new FakeEmptyHtmlRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertInternalType('string', $result);
        $this->assertEmpty($result);
    }

    public function testRenderDocument()
    {
        $block = new Document();
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertInternalType('string', $result);
        $this->assertContains('::blocks::', $result);
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
