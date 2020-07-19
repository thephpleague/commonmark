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
use League\CommonMark\Block\Element\HtmlBlock;
use League\CommonMark\Block\Renderer\HtmlBlockRenderer;
use League\CommonMark\Environment;
use League\CommonMark\Tests\Unit\FakeHtmlRenderer;
use League\CommonMark\Util\Configuration;
use PHPUnit\Framework\TestCase;

class HtmlBlockRendererTest extends TestCase
{
    /**
     * @var HtmlBlockRenderer
     */
    protected $renderer;

    protected function setUp(): void
    {
        $this->renderer = new HtmlBlockRenderer();
        $this->renderer->setConfiguration(new Configuration());
    }

    public function testRender()
    {
        /** @var HtmlBlock|\PHPUnit\Framework\MockObject\MockObject $block */
        $block = $this->getMockBuilder(\League\CommonMark\Block\Element\HtmlBlock::class)
            ->setConstructorArgs([HtmlBlock::TYPE_6_BLOCK_ELEMENT])
            ->getMock();
        $block->expects($this->any())
            ->method('getStringContent')
            ->will($this->returnValue('<button>Test</button>'));

        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertIsString($result);
        $this->assertStringContainsString('<button>Test</button>', $result);
    }

    public function testRenderAllowHtml()
    {
        $this->renderer->setConfiguration(new Configuration([
            'html_input' => Environment::HTML_INPUT_ALLOW,
        ]));

        /** @var HtmlBlock|\PHPUnit\Framework\MockObject\MockObject $block */
        $block = $this->getMockBuilder(\League\CommonMark\Block\Element\HtmlBlock::class)
            ->setConstructorArgs([HtmlBlock::TYPE_6_BLOCK_ELEMENT])
            ->getMock();
        $block->expects($this->any())
            ->method('getStringContent')
            ->will($this->returnValue('<button>Test</button>'));

        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertIsString($result);
        $this->assertStringContainsString('<button>Test</button>', $result);
    }

    public function testRenderEscapeHtml()
    {
        $this->renderer->setConfiguration(new Configuration([
            'html_input' => Environment::HTML_INPUT_ESCAPE,
        ]));

        /** @var HtmlBlock|\PHPUnit\Framework\MockObject\MockObject $block */
        $block = $this->getMockBuilder(\League\CommonMark\Block\Element\HtmlBlock::class)
            ->setConstructorArgs([HtmlBlock::TYPE_6_BLOCK_ELEMENT])
            ->getMock();
        $block->expects($this->any())
            ->method('getStringContent')
            ->will($this->returnValue('<button class="test">Test</button>'));

        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertIsString($result);
        $this->assertStringContainsString('&lt;button class="test"&gt;Test&lt;/button&gt;', $result);
    }

    public function testRenderStripHtml()
    {
        $this->renderer->setConfiguration(new Configuration([
            'html_input' => Environment::HTML_INPUT_STRIP,
        ]));

        /** @var HtmlBlock|\PHPUnit\Framework\MockObject\MockObject $block */
        $block = $this->getMockBuilder(\League\CommonMark\Block\Element\HtmlBlock::class)
            ->setConstructorArgs([HtmlBlock::TYPE_6_BLOCK_ELEMENT])
            ->getMock();
        $block->expects($this->any())
            ->method('getStringContent')
            ->will($this->returnValue('<button>Test</button>'));

        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertIsString($result);
        $this->assertEquals('', $result);
    }

    public function testRenderWithInvalidType()
    {
        $this->expectException(\InvalidArgumentException::class);

        $inline = $this->getMockForAbstractClass(BlockElement\AbstractBlock::class);
        $fakeRenderer = new FakeHtmlRenderer();

        $this->renderer->render($inline, $fakeRenderer);
    }
}
