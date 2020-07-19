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
use League\CommonMark\Block\Element\IndentedCode;
use League\CommonMark\Block\Renderer\IndentedCodeRenderer;
use League\CommonMark\Context;
use League\CommonMark\Environment;
use League\CommonMark\HtmlElement;
use League\CommonMark\Tests\Unit\FakeHtmlRenderer;
use PHPUnit\Framework\TestCase;

class IndentedCodeRendererTest extends TestCase
{
    /**
     * @var IndentedCodeRenderer
     */
    protected $renderer;

    protected function setUp(): void
    {
        $this->renderer = new IndentedCodeRenderer();
    }

    public function testRender()
    {
        $document = new Document();
        $context = new Context($document, new Environment());

        $block = new IndentedCode();
        $block->data['attributes'] = ['id' => 'foo'];
        $block->addLine('echo "hello world!";');

        $document->appendChild($block);
        $block->finalize($context, 1);

        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('pre', $result->getTagName());

        $code = $result->getContents(false);
        $this->assertTrue($code instanceof HtmlElement);
        $this->assertEquals('code', $code->getTagName());
        $this->assertNull($code->getAttribute('class'));
        $this->assertEquals(['id' => 'foo'], $code->getAllAttributes());
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
