<?php

declare(strict_types=1);

namespace League\CommonMark\Tests\Unit\Renderer;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Renderer\HtmlRenderer;
use League\CommonMark\Renderer\NoMatchingRendererException;
use League\CommonMark\Renderer\NodeRendererInterface;
use PHPUnit\Framework\TestCase;

final class HtmlRendererTest extends TestCase
{
    public function testRenderDocumentCallsDocumentRenderer(): void
    {
        $document = new Document();

        $documentRenderer = $this->createMock(NodeRendererInterface::class);
        $documentRenderer->method('render')->willReturn('::document::');

        $environment = new Environment();
        $environment->addRenderer(Document::class, $documentRenderer);
        $htmlRenderer = new HtmlRenderer($environment);

        $this->assertSame('::document::', (string) $htmlRenderer->renderDocument($document));
    }

    public function testRenderNodesWithBlocks(): void
    {
        $blockRenderer = $this->createMock(NodeRendererInterface::class);
        $blockRenderer->method('render')->willReturn('::block::');

        $environment = new Environment();
        $environment->addRenderer(Paragraph::class, $blockRenderer);

        $ast = new Document();
        $ast->appendChild(new Paragraph());
        $ast->appendChild(new Paragraph());

        $renderer = new HtmlRenderer($environment);
        $output   = $renderer->renderNodes($ast->children());

        $this->assertSame("::block::\n::block::", $output);
    }

    public function testRenderNodesWithInlines(): void
    {
        $inlineRenderer = $this->createMock(NodeRendererInterface::class);
        $inlineRenderer->method('render')->willReturn('::inline::');

        $environment = new Environment();
        $environment->addRenderer(Text::class, $inlineRenderer);

        $ast = new Paragraph();
        $ast->appendChild(new Text());
        $ast->appendChild(new Text());

        $renderer = new HtmlRenderer($environment);
        $output   = $renderer->renderNodes($ast->children());

        $this->assertSame('::inline::::inline::', $output);
    }

    public function testRenderNodesFallsBackWhenFirstRendererReturnsNull(): void
    {
        $renderer1 = $this->createMock(NodeRendererInterface::class);
        $renderer1->expects($this->once())->method('render')->willReturn(null);

        $renderer2 = $this->createMock(NodeRendererInterface::class);
        $renderer2->expects($this->once())->method('render')->willReturn('::result::');

        $environment = new Environment();
        $environment->addRenderer(Text::class, $renderer1);
        $environment->addRenderer(Text::class, $renderer2);

        $renderer = new HtmlRenderer($environment);
        $output   = $renderer->renderNodes([new Text()]);

        $this->assertSame('::result::', $output);
    }

    public function testRenderNodesWithMissingRenderer(): void
    {
        $this->expectException(NoMatchingRendererException::class);

        $environment = $this->createMock(EnvironmentInterface::class);
        $environment->method('getRenderersForClass')->willReturn([]);

        $renderer = new HtmlRenderer($environment);
        $renderer->renderNodes([new Paragraph()]);
    }
}
