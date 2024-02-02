<?php

declare(strict_types=1);

namespace League\CommonMark\Tests\Functional\Extension\FrontMatterExtension;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\FrontMatter\Data\FrontMatterDataParserInterface;
use League\CommonMark\Extension\FrontMatter\Data\LibYamlFrontMatterParser;
use League\CommonMark\Extension\FrontMatter\Data\SymfonyYamlFrontMatterParser;
use League\CommonMark\Extension\FrontMatter\Exception\InvalidFrontMatterException;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Output\RenderedContentInterface;
use League\CommonMark\Parser\MarkdownParser;
use League\CommonMark\Xml\XmlRenderer;
use PHPUnit\Framework\TestCase;

final class FrontMatterExtensionTest extends TestCase
{
    /**
     * @return array{array{FrontMatterDataParserInterface, bool}}
     */
    public static function parserProvider(): array
    {
        return [
            [new SymfonyYamlFrontMatterParser(), true],
            [new LibYamlFrontMatterParser(), LibYamlFrontMatterParser::capable() !== null],
        ];
    }

    protected function getEnvironment(FrontMatterDataParserInterface $parser): EnvironmentInterface
    {
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new FrontMatterExtension($parser));

        return $environment;
    }

    private function skipIfParserNotAvailable(FrontMatterDataParserInterface $parser, bool $shouldTest): void
    {
        if (! $shouldTest) {
            $this->markTestSkipped(\sprintf('Cannot test with %s due to missing prerequisites', \get_class($parser)));
        }
    }

    /**
     * @dataProvider parserProvider
     */
    public function testWithSampleData(FrontMatterDataParserInterface $parser, bool $shouldTest): void
    {
        $this->skipIfParserNotAvailable($parser, $shouldTest);

        $markdown     = <<<EOT
---
layout: post
title: Blogging Like a Hacker
redirect_from:
  - /blog/my-post
  - /blog/2020-04/my-post
---

# Hello World!

This is my awesome blog post

EOT;
        $expectedHtml = <<<EOT
<h1>Hello World!</h1>
<p>This is my awesome blog post</p>

EOT;

        $expectedFrontMatter = [
            'layout' => 'post',
            'title' => 'Blogging Like a Hacker',
            'redirect_from' => [
                '/blog/my-post',
                '/blog/2020-04/my-post',
            ],
        ];

        $converter = new MarkdownConverter($this->getEnvironment($parser));
        $result    = $converter->convert($markdown);

        $this->assertInstanceOf(RenderedContentWithFrontMatter::class, $result);
        $this->assertInstanceOf(\Stringable::class, $result);

        \assert($result instanceof RenderedContentWithFrontMatter);
        $this->assertSame($expectedFrontMatter, $result->getFrontMatter());

        $this->assertSame($expectedHtml, (string) $result->getContent());
        $this->assertSame($expectedHtml, (string) $result);

        $this->assertSame(1, $result->getDocument()->getStartLine());
        $this->assertSame(9, $result->getDocument()->firstChild()->getStartLine());
    }

    /**
     * @dataProvider parserProvider
     */
    public function testWithMultipleYamlDocuments(FrontMatterDataParserInterface $parser, bool $shouldTest): void
    {
        $this->skipIfParserNotAvailable($parser, $shouldTest);

        $markdown     = <<<EOT
---
layout: post
title: Blogging Like a Hacker
redirect_from:
  - /blog/my-post
  - /blog/2020-04/my-post
---

---
more_yaml: true
---

# Hello World!
EOT;
        $expectedHtml = <<<EOT
<hr />
<h2>more_yaml: true</h2>
<h1>Hello World!</h1>

EOT;

        $expectedFrontMatter = [
            'layout' => 'post',
            'title' => 'Blogging Like a Hacker',
            'redirect_from' => [
                '/blog/my-post',
                '/blog/2020-04/my-post',
            ],
        ];

        $converter = new MarkdownConverter($this->getEnvironment($parser));
        $result    = $converter->convert($markdown);

        $this->assertInstanceOf(RenderedContentWithFrontMatter::class, $result);
        $this->assertInstanceOf(\Stringable::class, $result);

        \assert($result instanceof RenderedContentWithFrontMatter);
        $this->assertSame($expectedFrontMatter, $result->getFrontMatter());

        $this->assertSame($expectedHtml, (string) $result->getContent());
        $this->assertSame($expectedHtml, (string) $result);

        $this->assertSame(1, $result->getDocument()->getStartLine());
        $this->assertSame(9, $result->getDocument()->firstChild()->getStartLine());
    }

    /**
     * @dataProvider parserProvider
     */
    public function testWithWindowsLineEndings(FrontMatterDataParserInterface $parser, bool $shouldTest): void
    {
        $this->skipIfParserNotAvailable($parser, $shouldTest);

        $markdown = "---\r\nfoo: bar\r\n---\r\n\r\n# Test";

        $expectedHtml        = "<h1>Test</h1>\n";
        $expectedFrontMatter = ['foo' => 'bar'];

        $converter = new MarkdownConverter($this->getEnvironment($parser));
        $result    = $converter->convertToHtml($markdown);

        $this->assertInstanceOf(RenderedContentWithFrontMatter::class, $result);
        $this->assertInstanceOf(\Stringable::class, $result);

        \assert($result instanceof RenderedContentWithFrontMatter);
        $this->assertSame($expectedFrontMatter, $result->getFrontMatter());

        $this->assertSame($expectedHtml, (string) $result->getContent());
        $this->assertSame($expectedHtml, (string) $result);

        $this->assertSame(1, $result->getDocument()->getStartLine());
        $this->assertSame(5, $result->getDocument()->firstChild()->getStartLine());
    }

    /**
     * @dataProvider parserProvider
     */
    public function testWithNoFrontMatter(FrontMatterDataParserInterface $parser, bool $shouldTest): void
    {
        $this->skipIfParserNotAvailable($parser, $shouldTest);

        $markdown  = '# Hello World!';
        $converter = new MarkdownConverter($this->getEnvironment($parser));
        $result    = $converter->convert($markdown);

        $this->assertInstanceOf(RenderedContentInterface::class, $result);
        $this->assertNotInstanceOf(RenderedContentWithFrontMatter::class, $result);
        $this->assertInstanceOf(\Stringable::class, $result);

        $expectedHtml = "<h1>Hello World!</h1>\n";

        $this->assertSame($expectedHtml, $result->getContent());
        $this->assertSame($expectedHtml, (string) $result);

        $this->assertSame(1, $result->getDocument()->getStartLine());
        $this->assertSame(1, $result->getDocument()->firstChild()->getStartLine());
    }

    /**
     * @dataProvider parserProvider
     */
    public function testWithInvalidYaml(FrontMatterDataParserInterface $parser, bool $shouldTest): void
    {
        $this->skipIfParserNotAvailable($parser, $shouldTest);

        $this->expectException(InvalidFrontMatterException::class);

        $markdown  = <<<EOT
---
  this: list
    is: not
 valid:::
---

# Oh no!

EOT;
        $converter = new MarkdownConverter($this->getEnvironment($parser));
        $converter->convert($markdown);
    }

    /**
     * @dataProvider parserProvider
     */
    public function testRenderXml(FrontMatterDataParserInterface $parser, bool $shouldTest): void
    {
        $this->skipIfParserNotAvailable($parser, $shouldTest);

        $markdown = <<<MD
---
layout: post
title: Blogging Like a Hacker
redirect_from:
- /blog/my-post
- /blog/2020-04/my-post
---

# Hello World!

This is my awesome blog post
MD;

        $expectedXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<document xmlns="http://commonmark.org/xml/1.0">
    <heading level="1">
        <text>Hello World!</text>
    </heading>
    <paragraph>
        <text>This is my awesome blog post</text>
    </paragraph>
</document>
XML;

        $environment = $this->getEnvironment($parser);
        $document    = (new MarkdownParser($environment))->parse($markdown);
        $xml         = (new XmlRenderer($environment))->renderDocument($document)->getContent();

        $this->assertSame($expectedXml, \rtrim($xml));
    }
}
