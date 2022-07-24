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

use League\CommonMark\Environment\Environment;
use League\CommonMark\Exception\InvalidArgumentException;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\HtmlBlock;
use League\CommonMark\Extension\CommonMark\Renderer\Block\HtmlBlockRenderer;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Tests\Unit\Renderer\FakeChildNodeRenderer;
use League\CommonMark\Util\HtmlFilter;
use League\Config\ConfigurationInterface;
use PHPUnit\Framework\TestCase;

final class HtmlBlockRendererTest extends TestCase
{
    private HtmlBlockRenderer $renderer;

    protected function setUp(): void
    {
        $this->renderer = new HtmlBlockRenderer();
        $this->renderer->setConfiguration($this->createConfiguration());
    }

    public function testRender(): void
    {
        $block = new HtmlBlock(HtmlBlock::TYPE_6_BLOCK_ELEMENT);
        $block->setLiteral('<button>Test</button>');

        $fakeRenderer = new FakeChildNodeRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertIsString($result);
        $this->assertStringContainsString('<button>Test</button>', $result);
    }

    public function testRenderAllowHtml(): void
    {
        $this->renderer->setConfiguration($this->createConfiguration([
            'html_input' => HtmlFilter::ALLOW,
        ]));

        $block = new HtmlBlock(HtmlBlock::TYPE_6_BLOCK_ELEMENT);
        $block->setLiteral('<button>Test</button>');

        $fakeRenderer = new FakeChildNodeRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertIsString($result);
        $this->assertStringContainsString('<button>Test</button>', $result);
    }

    public function testRenderEscapeHtml(): void
    {
        $this->renderer->setConfiguration($this->createConfiguration([
            'html_input' => HtmlFilter::ESCAPE,
        ]));

        $block = new HtmlBlock(HtmlBlock::TYPE_6_BLOCK_ELEMENT);
        $block->setLiteral('<button class="test">Test</button>');

        $fakeRenderer = new FakeChildNodeRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertIsString($result);
        $this->assertStringContainsString('&lt;button class="test"&gt;Test&lt;/button&gt;', $result);
    }

    public function testRenderStripHtml(): void
    {
        $this->renderer->setConfiguration($this->createConfiguration([
            'html_input' => HtmlFilter::STRIP,
        ]));

        $block = new HtmlBlock(HtmlBlock::TYPE_6_BLOCK_ELEMENT);
        $block->setLiteral('<button>Test</button>');

        $fakeRenderer = new FakeChildNodeRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertIsString($result);
        $this->assertEquals('', $result);
    }

    public function testRenderWithInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $inline       = $this->getMockForAbstractClass(AbstractBlock::class);
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
