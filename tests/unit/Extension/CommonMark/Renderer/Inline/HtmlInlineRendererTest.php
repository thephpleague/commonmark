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

namespace League\CommonMark\Tests\Unit\Extension\CommonMark\Renderer\Inline;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Exception\InvalidArgumentException;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Inline\HtmlInline;
use League\CommonMark\Extension\CommonMark\Renderer\Inline\HtmlInlineRenderer;
use League\CommonMark\Node\Inline\AbstractInline;
use League\CommonMark\Tests\Unit\Renderer\FakeChildNodeRenderer;
use League\CommonMark\Util\HtmlFilter;
use League\Config\ConfigurationInterface;
use PHPUnit\Framework\TestCase;

final class HtmlInlineRendererTest extends TestCase
{
    private HtmlInlineRenderer $renderer;

    protected function setUp(): void
    {
        $this->renderer = new HtmlInlineRenderer();
        $this->renderer->setConfiguration($this->createConfiguration(['html_input' => HtmlFilter::ALLOW]));
    }

    public function testRender(): void
    {
        $inline       = new HtmlInline('<h1>Test</h1>');
        $fakeRenderer = new FakeChildNodeRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertIsString($result);
        $this->assertStringContainsString('<h1>Test</h1>', $result);
    }

    public function testRenderAllowHtml(): void
    {
        $this->renderer->setConfiguration($this->createConfiguration([
            'html_input' => HtmlFilter::ALLOW,
        ]));

        $inline       = new HtmlInline('<h1>Test</h1>');
        $fakeRenderer = new FakeChildNodeRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertIsString($result);
        $this->assertStringContainsString('<h1>Test</h1>', $result);
    }

    public function testRenderEscapeHtml(): void
    {
        $this->renderer->setConfiguration($this->createConfiguration([
            'html_input' => HtmlFilter::ESCAPE,
        ]));

        $inline       = new HtmlInline('<h1 class="test">Test</h1>');
        $fakeRenderer = new FakeChildNodeRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertIsString($result);
        $this->assertStringContainsString('&lt;h1 class="test"&gt;Test&lt;/h1&gt;', $result);
    }

    public function testRenderStripHtml(): void
    {
        $this->renderer->setConfiguration($this->createConfiguration([
            'html_input' => HtmlFilter::STRIP,
        ]));

        $inline       = new HtmlInline('<h1>Test</h1>');
        $fakeRenderer = new FakeChildNodeRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertIsString($result);
        $this->assertEquals('', $result);
    }

    public function testRenderWithInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $inline       = $this->getMockForAbstractClass(AbstractInline::class);
        $fakeRenderer = new FakeChildNodeRenderer();

        $this->renderer->render($inline, $fakeRenderer);
    }

    /**
     * @param array<string, mixed> $values
     */
    private function createConfiguration(array $values = []): ConfigurationInterface
    {
        $config = Environment::createDefaultConfiguration();
        (new CommonMarkCoreExtension())->configureSchema($config);
        $config->merge($values);

        return $config->reader();
    }
}
