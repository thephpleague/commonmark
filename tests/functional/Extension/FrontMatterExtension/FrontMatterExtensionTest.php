<?php

declare(strict_types=1);

namespace League\CommonMark\Tests\Functional\Extension\FrontMatterExtension;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
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
    private Environment $environment;

    protected function setUp(): void
    {
        $this->environment = new Environment();
        $this->environment->addExtension(new CommonMarkCoreExtension());
        $this->environment->addExtension(new FrontMatterExtension());
    }

    public function testWithSampleData(): void
    {
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

        $converter = new MarkdownConverter($this->environment);
        $result    = $converter->convertToHtml($markdown);

        $this->assertInstanceOf(RenderedContentWithFrontMatter::class, $result);
        $this->assertInstanceOf(\Stringable::class, $result);

        \assert($result instanceof RenderedContentWithFrontMatter);
        $this->assertSame($expectedFrontMatter, $result->getFrontMatter());

        $this->assertSame($expectedHtml, (string) $result->getContent());
        $this->assertSame($expectedHtml, (string) $result);

        $this->assertSame(1, $result->getDocument()->getStartLine());
        $this->assertSame(9, $result->getDocument()->firstChild()->getStartLine());
    }

    public function testWithMultipleYamlDocuments(): void
    {
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

        $converter = new MarkdownConverter($this->environment);
        $result    = $converter->convertToHtml($markdown);

        $this->assertInstanceOf(RenderedContentWithFrontMatter::class, $result);
        $this->assertInstanceOf(\Stringable::class, $result);

        \assert($result instanceof RenderedContentWithFrontMatter);
        $this->assertSame($expectedFrontMatter, $result->getFrontMatter());

        $this->assertSame($expectedHtml, (string) $result->getContent());
        $this->assertSame($expectedHtml, (string) $result);

        $this->assertSame(1, $result->getDocument()->getStartLine());
        $this->assertSame(9, $result->getDocument()->firstChild()->getStartLine());
    }

    public function testWithNoFrontMatter(): void
    {
        $markdown  = '# Hello World!';
        $converter = new MarkdownConverter($this->environment);
        $result    = $converter->convertToHtml($markdown);

        $this->assertInstanceOf(RenderedContentInterface::class, $result);
        $this->assertNotInstanceOf(RenderedContentWithFrontMatter::class, $result);
        $this->assertInstanceOf(\Stringable::class, $result);

        $expectedHtml = "<h1>Hello World!</h1>\n";

        $this->assertSame($expectedHtml, $result->getContent());
        $this->assertSame($expectedHtml, (string) $result);

        $this->assertSame(1, $result->getDocument()->getStartLine());
        $this->assertSame(1, $result->getDocument()->firstChild()->getStartLine());
    }

    public function testWithInvalidYaml(): void
    {
        $this->expectException(InvalidFrontMatterException::class);

        $markdown  = <<<EOT
---
  this: list
    is: not
 valid:::
---

# Oh no!

EOT;
        $converter = new MarkdownConverter($this->environment);
        $converter->convertToHtml($markdown);
    }

    public function testRenderXml(): void
    {
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

        $document = (new MarkdownParser($this->environment))->parse($markdown);
        $xml      = (new XmlRenderer($this->environment))->renderDocument($document)->getContent();

        $this->assertSame($expectedXml, \rtrim($xml));
    }
}
