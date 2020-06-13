<?php

declare(strict_types=1);

namespace League\CommonMark\Tests\Functional\Extension\FrontMatterExtension;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\FrontMatter\Exception\InvalidFrontMatterException;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\Output\RenderedContentInterface;
use PHPUnit\Framework\TestCase;

final class FrontMatterExtensionTest extends TestCase
{
    /** @var Environment */
    private $environment;

    protected function setUp(): void
    {
        $this->environment = Environment::createCommonMarkEnvironment();
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

        $converter = new CommonMarkConverter([], $this->environment);
        $result    = $converter->convertToHtml($markdown);

        $this->assertInstanceOf(RenderedContentWithFrontMatter::class, $result);
        $this->assertInstanceOf(\Stringable::class, $result);

        \assert($result instanceof RenderedContentWithFrontMatter);
        $this->assertSame($expectedFrontMatter, $result->getFrontMatter());

        $this->assertSame($expectedHtml, (string) $result->getContent());
        $this->assertSame($expectedHtml, (string) $result);
    }

    public function testWithNoFrontMatter(): void
    {
        $markdown  = '# Hello World!';
        $converter = new CommonMarkConverter([], $this->environment);
        $result    = $converter->convertToHtml($markdown);

        $this->assertInstanceOf(RenderedContentInterface::class, $result);
        $this->assertNotInstanceOf(RenderedContentWithFrontMatter::class, $result);
        $this->assertInstanceOf(\Stringable::class, $result);

        $expectedHtml = "<h1>Hello World!</h1>\n";

        $this->assertSame($expectedHtml, (string) $result->getContent());
        $this->assertSame($expectedHtml, (string) $result);
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
        $converter = new CommonMarkConverter([], $this->environment);
        $converter->convertToHtml($markdown);
    }
}
