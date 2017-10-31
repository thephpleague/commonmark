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

    protected function setUp()
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
        $this->assertContains('hello world', $code->getContents(true));
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
