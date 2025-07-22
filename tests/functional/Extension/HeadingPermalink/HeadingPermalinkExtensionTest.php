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

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkProcessor;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkRenderer;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Parser\MarkdownParser;
use League\CommonMark\Xml\XmlRenderer;
use League\Config\Exception\InvalidConfigurationException;
use PHPUnit\Framework\TestCase;

final class HeadingPermalinkExtensionTest extends TestCase
{
    /**
     * @dataProvider dataProviderForTestHeadingPermalinksWithDefaultOptions
     */
    public function testHeadingPermalinksWithDefaultOptions(string $input, string $expected): void
    {
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new HeadingPermalinkExtension());

        $converter = new MarkdownConverter($environment);

        $this->assertEquals($expected, \trim((string) $converter->convert($input)));
    }

    public static function dataProviderForTestHeadingPermalinksWithDefaultOptions(): \Generator
    {
        yield ['# Hello World!', \sprintf('<h1><a id="content-hello-world" href="#content-hello-world" class="heading-permalink" aria-hidden="true" title="Permalink">%s</a>Hello World!</h1>', HeadingPermalinkRenderer::DEFAULT_SYMBOL)];
        yield ['# Hello *World*', \sprintf('<h1><a id="content-hello-world" href="#content-hello-world" class="heading-permalink" aria-hidden="true" title="Permalink">%s</a>Hello <em>World</em></h1>', HeadingPermalinkRenderer::DEFAULT_SYMBOL)];
        yield ['# Hello `World`', \sprintf('<h1><a id="content-hello-world" href="#content-hello-world" class="heading-permalink" aria-hidden="true" title="Permalink">%s</a>Hello <code>World</code></h1>', HeadingPermalinkRenderer::DEFAULT_SYMBOL)];
        yield ["Test\n----", \sprintf('<h2><a id="content-test" href="#content-test" class="heading-permalink" aria-hidden="true" title="Permalink">%s</a>Test</h2>', HeadingPermalinkRenderer::DEFAULT_SYMBOL)];
        yield ["# Hello World!\n\n# Hello World!", \sprintf("<h1><a id=\"content-hello-world\" href=\"#content-hello-world\" class=\"heading-permalink\" aria-hidden=\"true\" title=\"Permalink\">%s</a>Hello World!</h1>\n<h1><a id=\"content-hello-world-1\" href=\"#content-hello-world-1\" class=\"heading-permalink\" aria-hidden=\"true\" title=\"Permalink\">%s</a>Hello World!</h1>", HeadingPermalinkRenderer::DEFAULT_SYMBOL, HeadingPermalinkRenderer::DEFAULT_SYMBOL)];
    }

    /**
     * @dataProvider dataProviderForTestHeadingPermalinksWithCustomOptions
     */
    public function testHeadingPermalinksWithCustomOptions(string $input, string $expected): void
    {
        $environment = new Environment([
            'heading_permalink' => [
                'html_class'      => 'custom-class',
                'id_prefix'       => 'custom-id-prefix',
                'fragment_prefix' => 'custom-fragment-prefix',
                // Ensure multiple characters are allowed (including multibyte) and special HTML characters are escaped.
                'symbol'          => '¶ 🦄️ <3 You',
                'insert'          => HeadingPermalinkProcessor::INSERT_AFTER,
                'title'           => 'Link',
                'aria_hidden'     => false,
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new HeadingPermalinkExtension());

        $converter = new MarkdownConverter($environment);

        $this->assertEquals($expected, \trim((string) $converter->convert($input)));
    }

    public static function dataProviderForTestHeadingPermalinksWithCustomOptions(): \Generator
    {
        yield ['# Hello World!', '<h1>Hello World!<a id="custom-id-prefix-hello-world" href="#custom-fragment-prefix-hello-world" class="custom-class" title="Link">¶ 🦄️ &lt;3 You</a></h1>'];
        yield ['# Hello *World*', '<h1>Hello <em>World</em><a id="custom-id-prefix-hello-world" href="#custom-fragment-prefix-hello-world" class="custom-class" title="Link">¶ 🦄️ &lt;3 You</a></h1>'];
        yield ["Test\n----", '<h2>Test<a id="custom-id-prefix-test" href="#custom-fragment-prefix-test" class="custom-class" title="Link">¶ 🦄️ &lt;3 You</a></h2>'];
    }

    public function testHeadingPermalinksWithEmptyPrefixes(): void
    {
        $environment = new Environment([
            'heading_permalink' => [
                'id_prefix' => '',
                'fragment_prefix' => '',
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new HeadingPermalinkExtension());

        $converter = new MarkdownConverter($environment);

        $input    = '# Hello World!';
        $expected = \sprintf('<h1><a id="hello-world" href="#hello-world" class="heading-permalink" aria-hidden="true" title="Permalink">%s</a>Hello World!</h1>', HeadingPermalinkRenderer::DEFAULT_SYMBOL);

        $this->assertEquals($expected, \trim((string) $converter->convert($input)));
    }

    public function testHeadingPermalinksWithEmptySymbol(): void
    {
        $environment = new Environment([
            'heading_permalink' => [
                'symbol' => '',
                'id_prefix' => '',
                'fragment_prefix' => '',
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new HeadingPermalinkExtension());

        $converter = new MarkdownConverter($environment);

        $input    = '# Hello World!';
        $expected = '<h1><a id="hello-world" href="#hello-world" class="heading-permalink" aria-hidden="true" title="Permalink"></a>Hello World!</h1>';

        $this->assertEquals($expected, \trim((string) $converter->convert($input)));
    }

    public function testHeadingPermalinksWithInvalidInsertConfigurationValue(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $environment = new Environment([
            'heading_permalink' => [
                'insert' => 'invalid value here',
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new HeadingPermalinkExtension());

        $converter = new MarkdownConverter($environment);
        $converter->convert('# This will fail');
    }

    public function testWithCustomLevels(): void
    {
        $environment = new Environment([
            'heading_permalink' => [
                'min_heading_level' => 2,
                'max_heading_level' => 3,
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new HeadingPermalinkExtension());

        $converter = new MarkdownConverter($environment);

        $input    = <<<EOT
# 1
## 2
### 3
#### 4
EOT;
        $expected = <<<EOT
<h1>1</h1>
<h2><a id="content-2" href="#content-2" class="heading-permalink" aria-hidden="true" title="Permalink">¶</a>2</h2>
<h3><a id="content-3" href="#content-3" class="heading-permalink" aria-hidden="true" title="Permalink">¶</a>3</h3>
<h4>4</h4>
EOT;

        $this->assertEquals($expected, \trim((string) $converter->convert($input)));
    }

    public function testHeadingPermalinksWithApplyIdToHeading(): void
    {
        $environment = new Environment([
            'heading_permalink' => [
                'apply_id_to_heading' => true,
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new HeadingPermalinkExtension());

        $converter = new MarkdownConverter($environment);

        $input    = '# Hello World!';
        $expected = \sprintf('<h1 id="content-hello-world"><a href="#content-hello-world" class="heading-permalink" aria-hidden="true" title="Permalink">%s</a>Hello World!</h1>', HeadingPermalinkRenderer::DEFAULT_SYMBOL);

        $this->assertEquals($expected, \trim((string) $converter->convert($input)));
    }

    public function testHeadingPermalinksWithApplyIdToHeadingAndClass(): void
    {
        $environment = new Environment([
            'heading_permalink' => [
                'apply_id_to_heading' => true,
                'heading_class' => 'heading-anchor',
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new HeadingPermalinkExtension());

        $converter = new MarkdownConverter($environment);

        $input    = '# Hello World!';
        $expected = \sprintf('<h1 id="content-hello-world" class="heading-anchor"><a href="#content-hello-world" class="heading-permalink" aria-hidden="true" title="Permalink">%s</a>Hello World!</h1>', HeadingPermalinkRenderer::DEFAULT_SYMBOL);

        $this->assertEquals($expected, \trim((string) $converter->convert($input)));
    }

    public function testHeadingPermalinksWithApplyIdToHeadingWithoutLink(): void
    {
        $environment = new Environment([
            'heading_permalink' => [
                'insert' => HeadingPermalinkProcessor::INSERT_NONE,
                'apply_id_to_heading' => true,
                'heading_class' => 'heading-anchor',
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new HeadingPermalinkExtension());

        $converter = new MarkdownConverter($environment);

        $input    = '# Hello World!';
        $expected = '<h1 id="content-hello-world" class="heading-anchor">Hello World!</h1>';

        $this->assertEquals($expected, \trim((string) $converter->convert($input)));
    }

    /**
     * @dataProvider dataProviderForTestHeadingPermalinksWithBlacklist
     */
    public function testHeadingPermalinksWithBlacklist(string $input, string $expected): void
    {
        $environment = new Environment([
            'heading_permalink' => [
                'id_blacklist' => ['example-id', 'another-example'],
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new HeadingPermalinkExtension());

        $converter = new MarkdownConverter($environment);

        $this->assertEquals($expected, \trim((string) $converter->convert($input)));
    }

    public static function dataProviderForTestHeadingPermalinksWithBlacklist(): \Generator
    {
        $symbol = HeadingPermalinkRenderer::DEFAULT_SYMBOL;
        
        // Blacklisted ID gets suffix -1
        yield ['# Example ID', \sprintf('<h1><a id="content-example-id-1" href="#content-example-id-1" class="heading-permalink" aria-hidden="true" title="Permalink">%s</a>Example ID</h1>', $symbol)];
        
        // Another blacklisted ID gets suffix -1
        yield ['# Another Example', \sprintf('<h1><a id="content-another-example-1" href="#content-another-example-1" class="heading-permalink" aria-hidden="true" title="Permalink">%s</a>Another Example</h1>', $symbol)];
        
        // Non-blacklisted ID works normally
        yield ['# Regular Heading', \sprintf('<h1><a id="content-regular-heading" href="#content-regular-heading" class="heading-permalink" aria-hidden="true" title="Permalink">%s</a>Regular Heading</h1>', $symbol)];
        
        // Multiple headings with same blacklisted ID get incremental suffixes
        yield ["# Example ID\n\n# Example ID", \sprintf("<h1><a id=\"content-example-id-1\" href=\"#content-example-id-1\" class=\"heading-permalink\" aria-hidden=\"true\" title=\"Permalink\">%s</a>Example ID</h1>\n<h1><a id=\"content-example-id-2\" href=\"#content-example-id-2\" class=\"heading-permalink\" aria-hidden=\"true\" title=\"Permalink\">%s</a>Example ID</h1>", $symbol, $symbol)];
    }

    public function testHeadingPermalinksWithRawTextInBlacklist(): void
    {
        $environment = new Environment([
            'heading_permalink' => [
                'id_blacklist' => ['User Guide', 'About Us'], // Raw text in blacklist
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new HeadingPermalinkExtension());

        $converter = new MarkdownConverter($environment);

        $input = '# User Guide';
        $expected = \sprintf('<h1><a id="content-user-guide-1" href="#content-user-guide-1" class="heading-permalink" aria-hidden="true" title="Permalink">%s</a>User Guide</h1>', HeadingPermalinkRenderer::DEFAULT_SYMBOL);

        $this->assertEquals($expected, \trim((string) $converter->convert($input)));
    }

    public function testHeadingPermalinksWithBlacklistAndCustomPrefix(): void
    {
        $environment = new Environment([
            'heading_permalink' => [
                'id_prefix' => 'my-prefix',
                'fragment_prefix' => 'my-prefix',
                'id_blacklist' => ['hello-world'],
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new HeadingPermalinkExtension());

        $converter = new MarkdownConverter($environment);

        $input = '# Hello World';
        $expected = \sprintf('<h1><a id="my-prefix-hello-world-1" href="#my-prefix-hello-world-1" class="heading-permalink" aria-hidden="true" title="Permalink">%s</a>Hello World</h1>', HeadingPermalinkRenderer::DEFAULT_SYMBOL);

        $this->assertEquals($expected, \trim((string) $converter->convert($input)));
    }

    public function testHeadingPermalinksWithBlacklistAndEmptyPrefix(): void
    {
        $environment = new Environment([
            'heading_permalink' => [
                'id_prefix' => '',
                'fragment_prefix' => '',
                'id_blacklist' => ['hello-world'],
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new HeadingPermalinkExtension());

        $converter = new MarkdownConverter($environment);

        $input = '# Hello World';
        $expected = \sprintf('<h1><a id="hello-world-1" href="#hello-world-1" class="heading-permalink" aria-hidden="true" title="Permalink">%s</a>Hello World</h1>', HeadingPermalinkRenderer::DEFAULT_SYMBOL);

        $this->assertEquals($expected, \trim((string) $converter->convert($input)));
    }


    public function testXml(): void
    {
        $md = '# Hello *World*';

        $expectedXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<document xmlns="http://commonmark.org/xml/1.0">
    <heading level="1">
        <heading_permalink slug="hello-world" />
        <text>Hello </text>
        <emph>
            <text>World</text>
        </emph>
    </heading>
</document>
XML;

        $environment = new Environment([
            'heading_permalink' => [
                'id_prefix' => '',
                'fragment_prefix' => '',
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new HeadingPermalinkExtension());

        $document = (new MarkdownParser($environment))->parse($md);

        $this->assertSame($expectedXml, \rtrim((new XmlRenderer($environment))->renderDocument($document)->getContent()));
    }
}
