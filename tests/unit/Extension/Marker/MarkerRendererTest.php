<?php

declare(strict_types=1);

namespace League\CommonMark\Tests\Unit\Extension\Marker;

use League\CommonMark\Extension\Marker\Marker;
use League\CommonMark\Extension\Marker\MarkerRenderer;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Tests\Unit\Renderer\FakeChildNodeRenderer;
use League\CommonMark\Util\HtmlElement;
use PHPUnit\Framework\TestCase;

final class MarkerRendererTest extends TestCase
{
    private MarkerRenderer $renderer;

    protected function setUp(): void
    {
        $this->renderer = new MarkerRenderer();
    }

    public function testRender(): void
    {
        $inline = new Marker('==');
        $inline->data->set('attributes/id', 'some"&amp;id');
        $fakeRenderer = new FakeChildNodeRenderer();
        $fakeRenderer->pretendChildrenExist();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('mark', $result->getTagName());
        $this->assertStringContainsString('::children::', $result->getContents(true));
        $this->assertEquals(['id' => 'some"&amp;id'], $result->getAllAttributes());
    }

    public function testRenderWithInvalidNodeType(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $inline = new Text('ruh roh');
        $fakeRenderer = new FakeChildNodeRenderer();

        $this->renderer->render($inline, $fakeRenderer);
    }
}