<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Functional\Extension\HeadingPermalink;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkRenderer;
use PHPUnit\Framework\TestCase;

final class HeadingPermalinkExtensionTest extends TestCase
{
    /**
     * @dataProvider dataProviderForTestHeadingPermalinksWithDefaultOptions
     */
    public function testHeadingPermalinksWithDefaultOptions(string $input, string $expected): void
    {
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new HeadingPermalinkExtension());

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, \trim((string) $converter->convertToHtml($input)));
    }

    public function dataProviderForTestHeadingPermalinksWithDefaultOptions(): \Generator
    {
        yield ['# Hello World!', \sprintf('<h1><a id="user-content-hello-world" href="#hello-world" name="hello-world" class="heading-permalink" aria-hidden="true" title="Permalink">%s</a>Hello World!</h1>', HeadingPermalinkRenderer::DEFAULT_SYMBOL)];
        yield ['# Hello *World*', \sprintf('<h1><a id="user-content-hello-world" href="#hello-world" name="hello-world" class="heading-permalink" aria-hidden="true" title="Permalink">%s</a>Hello <em>World</em></h1>', HeadingPermalinkRenderer::DEFAULT_SYMBOL)];
        yield ['# Hello `World`', \sprintf('<h1><a id="user-content-hello-world" href="#hello-world" name="hello-world" class="heading-permalink" aria-hidden="true" title="Permalink">%s</a>Hello <code>World</code></h1>', HeadingPermalinkRenderer::DEFAULT_SYMBOL)];
        yield ["Test\n----", \sprintf('<h2><a id="user-content-test" href="#test" name="test" class="heading-permalink" aria-hidden="true" title="Permalink">%s</a>Test</h2>', HeadingPermalinkRenderer::DEFAULT_SYMBOL)];
        yield ["# Hello World!\n\n# Hello World!", \sprintf("<h1><a id=\"user-content-hello-world\" href=\"#hello-world\" name=\"hello-world\" class=\"heading-permalink\" aria-hidden=\"true\" title=\"Permalink\">%s</a>Hello World!</h1>\n<h1><a id=\"user-content-hello-world-1\" href=\"#hello-world-1\" name=\"hello-world-1\" class=\"heading-permalink\" aria-hidden=\"true\" title=\"Permalink\">%s</a>Hello World!</h1>", HeadingPermalinkRenderer::DEFAULT_SYMBOL, HeadingPermalinkRenderer::DEFAULT_SYMBOL)];
    }

    /**
     * @dataProvider dataProviderForTestHeadingPermalinksWithCustomOptions
     */
    public function testHeadingPermalinksWithCustomOptions(string $input, string $expected): void
    {
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new HeadingPermalinkExtension());

        $config = [
            'heading_permalink' => [
                'html_class'     => 'custom-class',
                'id_prefix'      => 'custom-prefix',
                // Ensure multiple characters are allowed (including multibyte) and special HTML characters are escaped.
                'symbol'         => '¶ 🦄️ <3 You',
                'insert'         => 'after',
                'title'          => 'Link',
            ],
        ];

        $converter = new CommonMarkConverter($config, $environment);

        $this->assertEquals($expected, \trim((string) $converter->convertToHtml($input)));
    }

    public function dataProviderForTestHeadingPermalinksWithCustomOptions(): \Generator
    {
        yield ['# Hello World!', '<h1>Hello World!<a id="custom-prefix-hello-world" href="#hello-world" name="hello-world" class="custom-class" aria-hidden="true" title="Link">¶ 🦄️ &lt;3 You</a></h1>'];
        yield ['# Hello *World*', '<h1>Hello <em>World</em><a id="custom-prefix-hello-world" href="#hello-world" name="hello-world" class="custom-class" aria-hidden="true" title="Link">¶ 🦄️ &lt;3 You</a></h1>'];
        yield ["Test\n----", '<h2>Test<a id="custom-prefix-test" href="#test" name="test" class="custom-class" aria-hidden="true" title="Link">¶ 🦄️ &lt;3 You</a></h2>'];
    }

    public function testHeadingPermalinksWithEmptyIdPrefix(): void
    {
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new HeadingPermalinkExtension());

        $config = [
            'heading_permalink' => [
                'id_prefix' => '',
            ],
        ];

        $converter = new CommonMarkConverter($config, $environment);

        $input    = '# Hello World!';
        $expected = \sprintf('<h1><a id="hello-world" href="#hello-world" name="hello-world" class="heading-permalink" aria-hidden="true" title="Permalink">%s</a>Hello World!</h1>', HeadingPermalinkRenderer::DEFAULT_SYMBOL);

        $this->assertEquals($expected, \trim((string) $converter->convertToHtml($input)));
    }

    public function testHeadingPermalinksWithEmptySymbol(): void
    {
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new HeadingPermalinkExtension());

        $config = [
            'heading_permalink' => [
                'symbol' => '',
            ],
        ];

        $converter = new CommonMarkConverter($config, $environment);

        $input    = '# Hello World!';
        $expected = '<h1><a id="user-content-hello-world" href="#hello-world" name="hello-world" class="heading-permalink" aria-hidden="true" title="Permalink"></a>Hello World!</h1>';

        $this->assertEquals($expected, \trim((string) $converter->convertToHtml($input)));
    }

    public function testHeadingPermalinksWithInvalidInsertConfigurationValue(): void
    {
        $this->expectException(\RuntimeException::class);

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new HeadingPermalinkExtension());

        $config = [
            'heading_permalink' => [
                'insert' => 'invalid value here',
            ],
        ];

        $converter = new CommonMarkConverter($config, $environment);
        $converter->convertToHtml('# This will fail');
    }

    public function testWithCustomLevels(): void
    {
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new HeadingPermalinkExtension());

        $config = [
            'heading_permalink' => [
                'min_heading_level' => 2,
                'max_heading_level' => 3,
            ],
        ];

        $converter = new CommonMarkConverter($config, $environment);

        $input    = <<<EOT
# 1
## 2
### 3
#### 4
EOT;
        $expected = <<<EOT
<h1>1</h1>
<h2><a id="user-content-2" href="#2" name="2" class="heading-permalink" aria-hidden="true" title="Permalink">¶</a>2</h2>
<h3><a id="user-content-3" href="#3" name="3" class="heading-permalink" aria-hidden="true" title="Permalink">¶</a>3</h3>
<h4>4</h4>
EOT;

        $this->assertEquals($expected, \trim((string) $converter->convertToHtml($input)));
    }
}
