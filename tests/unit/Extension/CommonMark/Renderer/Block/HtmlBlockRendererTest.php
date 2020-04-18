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

namespace League\CommonMark\Tests\Unit\Extension\CommonMark\Renderer\Block;

use League\CommonMark\Configuration\Configuration;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\Node\Block\HtmlBlock;
use League\CommonMark\Extension\CommonMark\Renderer\Block\HtmlBlockRenderer;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Tests\Unit\Renderer\FakeHtmlRenderer;
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
        /** @var HtmlBlock|\PHPUnit_Framework_MockObject_MockObject $block */
        $block = $this->getMockBuilder(HtmlBlock::class)
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

        /** @var HtmlBlock|\PHPUnit_Framework_MockObject_MockObject $block */
        $block = $this->getMockBuilder(HtmlBlock::class)
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

        /** @var HtmlBlock|\PHPUnit_Framework_MockObject_MockObject $block */
        $block = $this->getMockBuilder(HtmlBlock::class)
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

        /** @var HtmlBlock|\PHPUnit_Framework_MockObject_MockObject $block */
        $block = $this->getMockBuilder(HtmlBlock::class)
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

        $inline = $this->getMockForAbstractClass(AbstractBlock::class);
        $fakeRenderer = new FakeHtmlRenderer();

        $this->renderer->render($inline, $fakeRenderer);
    }
}
