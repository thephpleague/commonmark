<?php

namespace League\CommonMark\Tests\Unit;

use League\CommonMark\Block\Element\Paragraph;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\EnvironmentInterface;
use League\CommonMark\HtmlRenderer;
use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;
use PHPUnit\Framework\TestCase;

class HtmlRendererTest extends TestCase
{
    public function testRenderBlock()
    {
        $mockRenderer = $this->createMock(BlockRendererInterface::class);
        $mockRenderer->expects($this->once())->method('render')->willReturn(true);

        $environment = $this->createMock(EnvironmentInterface::class);
        $environment->method('getBlockRenderersForClass')->willReturn([$mockRenderer]);

        $renderer = new HtmlRenderer($environment);
        $renderer->renderBlock(new Paragraph());
    }

    public function testRenderBlockWithMissingRenderer()
    {
        $this->expectException(\RuntimeException::class);
        $environment = $this->createMock(EnvironmentInterface::class);
        $environment->method('getBlockRenderersForClass')->willReturn([]);

        $renderer = new HtmlRenderer($environment);
        $renderer->renderBlock(new Paragraph());
    }

    public function testRenderInline()
    {
        $mockRenderer = $this->createMock(InlineRendererInterface::class);
        $mockRenderer->expects($this->once())->method('render')->willReturn(true);

        $environment = $this->createMock(EnvironmentInterface::class);
        $environment->method('getInlineRenderersForClass')->willReturn([$mockRenderer]);

        $renderer = new HtmlRenderer($environment);
        $renderer->renderInline(new Text());
    }

    public function testRenderInlineWithMissingRenderer()
    {
        $this->expectException(\RuntimeException::class);

        $environment = $this->createMock(EnvironmentInterface::class);
        $environment->method('getInlineRenderersForClass')->willReturn([]);

        $renderer = new HtmlRenderer($environment);
        $renderer->renderInline(new Text());
    }
}
