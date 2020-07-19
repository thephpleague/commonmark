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

namespace League\CommonMark\Tests\Unit\Inline\Renderer;

use League\CommonMark\Environment;
use League\CommonMark\Inline\Element as InlineElement;
use League\CommonMark\Inline\Element\HtmlInline;
use League\CommonMark\Inline\Renderer\HtmlInlineRenderer;
use League\CommonMark\Tests\Unit\FakeHtmlRenderer;
use League\CommonMark\Util\Configuration;
use PHPUnit\Framework\TestCase;

class HtmlInlineRendererTest extends TestCase
{
    /**
     * @var HtmlInlineRenderer
     */
    protected $renderer;

    protected function setUp(): void
    {
        $this->renderer = new HtmlInlineRenderer();
        $this->renderer->setConfiguration(new Configuration());
    }

    public function testRender()
    {
        $inline = new HtmlInline('<h1>Test</h1>');
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertIsString($result);
        $this->assertStringContainsString('<h1>Test</h1>', $result);
    }

    public function testRenderAllowHtml()
    {
        $this->renderer->setConfiguration(new Configuration([
            'html_input' => Environment::HTML_INPUT_ALLOW,
        ]));

        $inline = new HtmlInline('<h1>Test</h1>');
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertIsString($result);
        $this->assertStringContainsString('<h1>Test</h1>', $result);
    }

    public function testRenderEscapeHtml()
    {
        $this->renderer->setConfiguration(new Configuration([
            'html_input' => Environment::HTML_INPUT_ESCAPE,
        ]));

        $inline = new HtmlInline('<h1 class="test">Test</h1>');
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertIsString($result);
        $this->assertStringContainsString('&lt;h1 class="test"&gt;Test&lt;/h1&gt;', $result);
    }

    public function testRenderStripHtml()
    {
        $this->renderer->setConfiguration(new Configuration([
            'html_input' => Environment::HTML_INPUT_STRIP,
        ]));

        $inline = new HtmlInline('<h1>Test</h1>');
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertIsString($result);
        $this->assertEquals('', $result);
    }

    public function testRenderWithInvalidType()
    {
        $this->expectException(\InvalidArgumentException::class);

        $inline = $this->getMockForAbstractClass(InlineElement\AbstractInline::class);
        $fakeRenderer = new FakeHtmlRenderer();

        $this->renderer->render($inline, $fakeRenderer);
    }
}
