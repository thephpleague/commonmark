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

namespace League\CommonMark\Tests\Unit\Extension\FrontMatter;

use League\CommonMark\Extension\FrontMatter\Data\FrontMatterDataParserInterface;
use League\CommonMark\Extension\FrontMatter\Data\SymfonyYamlFrontMatterParser;
use League\CommonMark\Extension\FrontMatter\FrontMatterParser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class FrontMatterParserTest extends TestCase
{
    public function testWithFrontMatter(): void
    {
        $markdown = <<<EOT
---
title: Hello World!
published: true
---
Yay
---
EOT;

        $parser = new FrontMatterParser(new SymfonyYamlFrontMatterParser());

        $parsedData = $parser->parse($markdown);

        $this->assertSame(['title' => 'Hello World!', 'published' => true], $parsedData->getFrontMatter());
        $this->assertSame("Yay\n---", $parsedData->getContent());
    }

    public function testWithFrontMatterThatsJustAString(): void
    {
        $markdown = <<<EOT
---
Hello World!
---
Yay
---
EOT;

        $parser = new FrontMatterParser(new SymfonyYamlFrontMatterParser());

        $parsedData = $parser->parse($markdown);

        $this->assertSame('Hello World!', $parsedData->getFrontMatter());
        $this->assertSame("Yay\n---", $parsedData->getContent());
    }

    public function testWithNoFrontMatter(): void
    {
        $markdown = <<<EOT
# Hello World!

---
This is not front matter
---
EOT;

        $parser = new FrontMatterParser(new SymfonyYamlFrontMatterParser());

        $parsedData = $parser->parse($markdown);

        $this->assertNull($parsedData->getFrontMatter());
        $this->assertSame($markdown, $parsedData->getContent());
    }

    public function testWithInvalidFrontMatterDelimiters(): void
    {
        $markdown = <<<EOT
---
This is a heading
-----------------
EOT;

        $parser = new FrontMatterParser(new SymfonyYamlFrontMatterParser());

        $parsedData = $parser->parse($markdown);

        $this->assertNull($parsedData->getFrontMatter());
        $this->assertSame($markdown, $parsedData->getContent());
    }

    public function testWithVeryLargeFrontMatter(): void
    {
        $value    = \str_repeat('a', 1100000);
        $markdown = "---\ntitle: \"" . $value . "\"\n---\n\nbody";

        $parsedData = (new FrontMatterParser(new SymfonyYamlFrontMatterParser()))->parse($markdown);

        $this->assertSame(['title' => $value], $parsedData->getFrontMatter());
        $this->assertSame('body', $parsedData->getContent());
    }

    /**
     * The scanner must find the delimiters exactly where /\A---\R.*?\R---\R/s does
     *
     * @dataProvider provideDelimiterEdgeCases
     */
    #[DataProvider('provideDelimiterEdgeCases')]
    public function testDelimitersAreFoundWhereTheOriginalRegexFoundThem(string $markdown): void
    {
        $expectedFrontMatter = null;
        $expectedContent     = $markdown;
        if (\preg_match('/\A---\R.*?\R---\R/s', $markdown, $matches) === 1) {
            $expectedFrontMatter = \preg_replace('/---\R$/', '', $matches[0]);
            $expectedContent     = \preg_replace('/\A\R+/', '', \substr($markdown, \strlen($matches[0])));
        }

        $dataParser = new class () implements FrontMatterDataParserInterface {
            public function parse(string $frontMatter): string
            {
                return $frontMatter;
            }
        };

        $parsedData = (new FrontMatterParser($dataParser))->parse($markdown);

        $this->assertSame($expectedFrontMatter, $parsedData->getFrontMatter());
        $this->assertSame($expectedContent, $parsedData->getContent());
    }

    /**
     * @return iterable<array{string}>
     */
    public static function provideDelimiterEdgeCases(): iterable
    {
        $newlines = ["\n", "\r\n", "\r", "\x0b", "\x0c"];
        foreach ($newlines as $a) {
            foreach ($newlines as $b) {
                yield [\sprintf('---%sa%s---%sbody', $a, $b, $a)];
                yield [\sprintf('---%s%s---%sbody', $a, $b, $b)];
                yield [\sprintf('---%sa%s---', $a, $b)];
            }

            yield ['---' . $a];
        }

        yield ["---\n---\nbody"];
        yield ["---\n\n---\nbody"];
        yield ["---\r\n---\r\nbody"];
        yield ["---\na\n----\nb\n---\n\n\nbody"];
        yield ["---\na---\nb\n---\nbody"];
        yield ["---\na\n---b\n---\nbody"];
        yield ["--\na\n---\nbody"];
        yield ["---a\nb\n---\nbody"];
        yield [" ---\na\n---\nbody"];
        yield ["---\na\n---"];
        yield ['---'];
        yield [''];
        yield ["---\nÅ---\nbody"];
    }
}
