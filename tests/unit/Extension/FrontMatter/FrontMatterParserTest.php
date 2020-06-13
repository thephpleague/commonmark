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

use League\CommonMark\Extension\FrontMatter\Data\SymfonyYamlFrontMatterParser;
use League\CommonMark\Extension\FrontMatter\FrontMatterParser;
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
}
