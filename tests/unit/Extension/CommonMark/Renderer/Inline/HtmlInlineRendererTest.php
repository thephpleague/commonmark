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

namespace League\CommonMark\Tests\Unit\Extension\CommonMark\Renderer\Inline;

use League\CommonMark\Configuration\Configuration;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\Node\Inline\HtmlInline;
use League\CommonMark\Extension\CommonMark\Renderer\Inline\HtmlInlineRenderer;
use League\CommonMark\Node\Inline\AbstractInline;
use League\CommonMark\Tests\Unit\Renderer\FakeHtmlRenderer;
use PHPUnit\Framework\TestCase;

class HtmlInlineRendererTest extends TestCase
{
    /**
     * @var HtmlInlineRenderer
     */
    protected $renderer;

    protected function setUp()
    {
        $this->renderer = new HtmlInlineRenderer();
        $this->renderer->setConfiguration(new Configuration());
    }

    public function testRender()
    {
        $inline = new HtmlInline('<h1>Test</h1>');
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertInternalType('string', $result);
        $this->assertContains('<h1>Test</h1>', $result);
    }

    public function testRenderAllowHtml()
    {
        $this->renderer->setConfiguration(new Configuration([
            'html_input' => Environment::HTML_INPUT_ALLOW,
        ]));

        $inline = new HtmlInline('<h1>Test</h1>');
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertInternalType('string', $result);
        $this->assertContains('<h1>Test</h1>', $result);
    }

    public function testRenderEscapeHtml()
    {
        $this->renderer->setConfiguration(new Configuration([
            'html_input' => Environment::HTML_INPUT_ESCAPE,
        ]));

        $inline = new HtmlInline('<h1 class="test">Test</h1>');
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertInternalType('string', $result);
        $this->assertContains('&lt;h1 class="test"&gt;Test&lt;/h1&gt;', $result);
    }

    public function testRenderStripHtml()
    {
        $this->renderer->setConfiguration(new Configuration([
            'html_input' => Environment::HTML_INPUT_STRIP,
        ]));

        $inline = new HtmlInline('<h1>Test</h1>');
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertInternalType('string', $result);
        $this->assertEquals('', $result);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRenderWithInvalidType()
    {
        $inline = $this->getMockForAbstractClass(AbstractInline::class);
        $fakeRenderer = new FakeHtmlRenderer();

        $this->renderer->render($inline, $fakeRenderer);
    }
}
