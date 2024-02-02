<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\CommonMark\Tests\Unit\Extension\DisallowedRawHtml;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\DisallowedRawHtml\DisallowedRawHtmlExtension;
use League\CommonMark\Extension\DisallowedRawHtml\DisallowedRawHtmlRenderer;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Tests\Unit\Renderer\FakeChildNodeRenderer;
use League\Config\ConfigurationInterface;
use PHPUnit\Framework\TestCase;

final class DisallowedRawHtmlRendererTest extends TestCase
{
    public function testWithEmptyHtml(): void
    {
        $mockRenderer = $this->createMock(NodeRendererInterface::class);
        $mockRenderer->method('render')->willReturn('');

        $renderer = new DisallowedRawHtmlRenderer($mockRenderer);
        $renderer->setConfiguration($this->createConfiguration());

        $this->assertSame('', $renderer->render($this->createMock(Node::class), new FakeChildNodeRenderer()));
    }

    /**
     * @dataProvider dataProviderForTestWithDefaultSettings
     */
    public function testWithDefaultSettings(string $input, string $expectedOutput): void
    {
        $mockRenderer = $this->createMock(NodeRendererInterface::class);
        $mockRenderer->method('render')->willReturn($input);

        $renderer = new DisallowedRawHtmlRenderer($mockRenderer);
        $renderer->setConfiguration($this->createConfiguration());

        $this->assertSame($expectedOutput, $renderer->render($this->createMock(Node::class), new FakeChildNodeRenderer()));
    }

    /**
     * @return iterable<mixed>
     */
    public static function dataProviderForTestWithDefaultSettings(): iterable
    {
        // Different tag variants
        yield ['<title>', '&lt;title>'];
        yield ['</title>', '&lt;/title>'];
        yield ['<title x="sdf">', '&lt;title x="sdf">'];
        yield ['<title/>', '&lt;title/>'];
        yield ['<title />', '&lt;title />'];

        // Other tags escaped by default
        yield ['<textarea>', '&lt;textarea>'];
        yield ['<style>', '&lt;style>'];
        yield ['<xmp>', '&lt;xmp>'];
        yield ['<iframe>', '&lt;iframe>'];
        yield ['<noembed>', '&lt;noembed>'];
        yield ['<noframes>', '&lt;noframes>'];
        yield ['<script>', '&lt;script>'];
        yield ['<plaintext>', '&lt;plaintext>'];

        // Tags not escaped by default
        yield ['<strong>', '<strong>'];
    }

    /**
     * @dataProvider dataProviderForTestWithCustomSettings
     */
    public function testWithCustomSettings(string $input, string $expectedOutput): void
    {
        $mockRenderer = $this->createMock(NodeRendererInterface::class);
        $mockRenderer->method('render')->willReturn($input);

        $renderer = new DisallowedRawHtmlRenderer($mockRenderer);
        $renderer->setConfiguration($this->createConfiguration([
            'disallowed_raw_html' => [
                'disallowed_tags' => [
                    'strong',
                ],
            ],
        ]));

        $this->assertSame($expectedOutput, $renderer->render($this->createMock(Node::class), new FakeChildNodeRenderer()));
    }

    /**
     * @return iterable<mixed>
     */
    public static function dataProviderForTestWithCustomSettings(): iterable
    {
        // Tags that I've configured to escape
        yield ['<strong>', '&lt;strong>'];
        yield ['</strong>', '&lt;/strong>'];
        yield ['<strong x="sdf">', '&lt;strong x="sdf">'];
        yield ['<strong/>', '&lt;strong/>'];
        yield ['<strong />', '&lt;strong />'];

        // Defaults that I didn't include in my custom config
        yield ['<title>', '<title>'];
        yield ['<textarea>', '<textarea>'];
        yield ['<style>', '<style>'];
        yield ['<xmp>', '<xmp>'];
        yield ['<iframe>', '<iframe>'];
        yield ['<noembed>', '<noembed>'];
        yield ['<noframes>', '<noframes>'];
        yield ['<script>', '<script>'];
        yield ['<plaintext>', '<plaintext>'];
    }

    /**
     * @param array<string, mixed> $values
     */
    private function createConfiguration(array $values = []): ConfigurationInterface
    {
        $config = Environment::createDefaultConfiguration();
        (new DisallowedRawHtmlExtension())->configureSchema($config);
        $config->merge($values);

        return $config->reader();
    }
}
