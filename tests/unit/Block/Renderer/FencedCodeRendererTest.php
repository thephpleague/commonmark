<?php

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

namespace League\CommonMark\Tests\Unit\Block\Renderer;

use League\CommonMark\Block\Element as BlockElement;
use League\CommonMark\Block\Element\Document;
use League\CommonMark\Block\Element\FencedCode;
use League\CommonMark\Block\Renderer\FencedCodeRenderer;
use League\CommonMark\Context;
use League\CommonMark\Environment;
use League\CommonMark\HtmlElement;
use League\CommonMark\Tests\Unit\FakeHtmlRenderer;
use PHPUnit\Framework\TestCase;

class FencedCodeRendererTest extends TestCase
{
    /**
     * @var FencedCodeRenderer
     */
    protected $renderer;

    protected function setUp(): void
    {
        $this->renderer = new FencedCodeRenderer();
    }

    public function testRenderWithLanguageSpecified()
    {
        $document = new Document();
        $context = new Context($document, new Environment());

        $block = new FencedCode(3, '~', 0);
        $block->addLine('php');
        $block->addLine('echo "hello world!";');
        $block->data['attributes'] = ['id' => 'foo', 'class' => 'bar'];

        $document->appendChild($block);
        $block->finalize($context, 1);

        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('pre', $result->getTagName());

        $code = $result->getContents(false);
        $this->assertTrue($code instanceof HtmlElement);
        $this->assertEquals('code', $code->getTagName());
        $this->assertStringContainsString('bar language-php', $code->getAttribute('class'));
        $this->assertStringContainsString('hello world', $code->getContents(true));
    }

    public function testRenderWithoutLanguageSpecified()
    {
        $document = new Document();
        $context = new Context($document, new Environment());

        $block = new FencedCode(3, '~', 0);
        $block->addLine('');
        $block->addLine('echo "hello world!";');
        $block->data['attributes'] = ['id' => 'foo', 'class' => 'bar'];

        $document->appendChild($block);
        $block->finalize($context, 1);

        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('pre', $result->getTagName());

        $code = $result->getContents(false);
        $this->assertTrue($code instanceof HtmlElement);
        $this->assertEquals('code', $code->getTagName());
        $this->assertEquals('bar', $code->getAttribute('class'));
        $this->assertStringContainsString('hello world', $code->getContents(true));
    }

    public function testRenderWithInvalidType()
    {
        $this->expectException(\InvalidArgumentException::class);

        $inline = $this->getMockForAbstractClass(BlockElement\AbstractBlock::class);
        $fakeRenderer = new FakeHtmlRenderer();

        $this->renderer->render($inline, $fakeRenderer);
    }
}
