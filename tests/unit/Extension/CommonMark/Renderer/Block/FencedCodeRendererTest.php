<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Extension\CommonMark\Renderer\Block;

use League\CommonMark\Exception\InvalidArgumentException;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Renderer\Block\FencedCodeRenderer;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Tests\Unit\Renderer\FakeChildNodeRenderer;
use League\CommonMark\Util\HtmlElement;
use PHPUnit\Framework\TestCase;

final class FencedCodeRendererTest extends TestCase
{
    private FencedCodeRenderer $renderer;

    protected function setUp(): void
    {
        $this->renderer = new FencedCodeRenderer();
    }

    public function testRenderWithLanguageSpecified(): void
    {
        $document = new Document();

        $block = new FencedCode(3, '~', 0);
        $block->setInfo('php');
        $block->setLiteral('echo "hello world!";');
        $block->data->set('attributes', ['id' => 'foo', 'class' => 'bar']);

        $document->appendChild($block);

        $fakeRenderer = new FakeChildNodeRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('pre', $result->getTagName());

        $code = $result->getContents(false);
        $this->assertTrue($code instanceof HtmlElement);
        $this->assertEquals('code', $code->getTagName());
        $this->assertStringContainsString('bar language-php', $code->getAttribute('class'));
        $this->assertStringContainsString('hello world', $code->getContents(true));
    }

    public function testRenderWithoutLanguageSpecified(): void
    {
        $document = new Document();

        $block = new FencedCode(3, '~', 0);
        $block->setLiteral('echo "hello world!";');
        $block->data->set('attributes', ['id' => 'foo', 'class' => 'bar']);

        $document->appendChild($block);

        $fakeRenderer = new FakeChildNodeRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('pre', $result->getTagName());

        $code = $result->getContents(false);
        $this->assertTrue($code instanceof HtmlElement);
        $this->assertEquals('code', $code->getTagName());
        $this->assertEquals('bar', $code->getAttribute('class'));
        $this->assertStringContainsString('hello world', $code->getContents(true));
    }

    public function testRenderWithInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $inline       = $this->getMockForAbstractClass(AbstractBlock::class);
        $fakeRenderer = new FakeChildNodeRenderer();

        $this->renderer->render($inline, $fakeRenderer);
    }
}
