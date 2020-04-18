<?php

namespace League\CommonMark\Tests\Unit\Renderer;

use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Renderer\Block\BlockRendererInterface;
use League\CommonMark\Renderer\HtmlRenderer;
use League\CommonMark\Renderer\Inline\InlineRendererInterface;
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
