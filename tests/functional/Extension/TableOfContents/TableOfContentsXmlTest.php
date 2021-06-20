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

namespace League\CommonMark\Tests\Functional\Extension\TableOfContents;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\TableOfContents\TableOfContentsExtension;
use League\CommonMark\Parser\MarkdownParser;
use League\CommonMark\Tests\Functional\AbstractLocalDataTest;
use League\CommonMark\Xml\XmlRenderer;

final class TableOfContentsXmlTest extends AbstractLocalDataTest
{
    private MarkdownParser $parser;

    private XmlRenderer $renderer;

    public function testWithSampleData(): void
    {
        $this->setUpConverter();

        foreach ($this->loadTests(__DIR__ . '/xml', 'sample', '.md', '.xml') as [$markdown, $xml, $testName]) {
            $this->assertMarkdownRendersToXml($markdown, $xml, $testName);
        }
    }

    public function testWithWeirdNestingLeavingItAsIs(): void
    {
        $this->setUpConverter([
            'table_of_contents' => [
                'normalize' => 'as-is',
            ],
        ]);

        foreach ($this->loadTests(__DIR__ . '/xml', 'weird-as-is', '.md', '.xml') as [$markdown, $xml, $testName]) {
            $this->assertMarkdownRendersToXml($markdown, $xml, $testName);
        }
    }

    public function testWithWeirdNestingWithRelativeNormalization(): void
    {
        $this->setUpConverter([
            'table_of_contents' => [
                'normalize' => 'relative',
            ],
        ]);

        foreach ($this->loadTests(__DIR__ . '/xml', 'weird-relative', '.md', '.xml') as [$markdown, $xml, $testName]) {
            $this->assertMarkdownRendersToXml($markdown, $xml, $testName);
        }
    }

    public function testWithWeirdNestingWithFlattenedNormalization(): void
    {
        $this->setUpConverter([
            'table_of_contents' => [
                'normalize' => 'flat',
            ],
        ]);

        foreach ($this->loadTests(__DIR__ . '/xml', 'weird-flattened', '.md', '.xml') as [$markdown, $xml, $testName]) {
            $this->assertMarkdownRendersToXml($markdown, $xml, $testName);
        }
    }

    public function testWithPositionTop(): void
    {
        $this->setUpConverter([
            'table_of_contents' => [
                'position' => 'top',
            ],
        ]);

        foreach ($this->loadTests(__DIR__ . '/xml', 'position-top', '.md', '.xml') as [$markdown, $xml, $testName]) {
            $this->assertMarkdownRendersToXml($markdown, $xml, $testName);
        }
    }

    public function testWithPositionBeforeHeadings(): void
    {
        $this->setUpConverter([
            'table_of_contents' => [
                'position' => 'before-headings',
            ],
        ]);

        foreach ($this->loadTests(__DIR__ . '/xml', 'position-before-headings', '.md', '.xml') as [$markdown, $xml, $testName]) {
            $this->assertMarkdownRendersToXml($markdown, $xml, $testName);
        }
    }

    public function testWithPositionPlaceholder(): void
    {
        $this->setUpConverter([
            'table_of_contents' => [
                'position'    => 'placeholder',
                'placeholder' => '[TOC]',
            ],
        ]);

        foreach ($this->loadTests(__DIR__ . '/xml', 'position-placeholder*', '.md', '.xml') as [$markdown, $xml, $testName]) {
            $this->assertMarkdownRendersToXml($markdown, $xml, $testName);
        }
    }

    public function testWithCustomClass(): void
    {
        $this->setUpConverter([
            'table_of_contents' => [
                'html_class' => 'markdown-toc',
            ],
        ]);

        foreach ($this->loadTests(__DIR__ . '/xml', 'custom-class', '.md', '.xml') as [$markdown, $xml, $testName]) {
            $this->assertMarkdownRendersToXml($markdown, $xml, $testName);
        }
    }

    public function testWithBulletedStyle(): void
    {
        $this->setUpConverter([
            'table_of_contents' => [
                'style' => 'bullet',
            ],
        ]);

        foreach ($this->loadTests(__DIR__ . '/xml', 'style-bullet', '.md', '.xml') as [$markdown, $xml, $testName]) {
            $this->assertMarkdownRendersToXml($markdown, $xml, $testName);
        }
    }

    public function testWithOrderedStyle(): void
    {
        $this->setUpConverter([
            'table_of_contents' => [
                'style' => 'ordered',
            ],
        ]);

        foreach ($this->loadTests(__DIR__ . '/xml', 'style-ordered', '.md', '.xml') as [$markdown, $xml, $testName]) {
            $this->assertMarkdownRendersToXml($markdown, $xml, $testName);
        }
    }

    public function testWithNoHeadings(): void
    {
        $this->setUpConverter();

        foreach ($this->loadTests(__DIR__ . '/xml', 'no-headings', '.md', '.xml') as [$markdown, $xml, $testName]) {
            $this->assertMarkdownRendersToXml($markdown, $xml, $testName);
        }
    }

    public function testWithSetextHeadings(): void
    {
        $this->setUpConverter();

        foreach ($this->loadTests(__DIR__ . '/xml', 'setext-headings', '.md', '.xml') as [$markdown, $xml, $testName]) {
            $this->assertMarkdownRendersToXml($markdown, $xml, $testName);
        }
    }

    public function testMinMaxHeadingLevels(): void
    {
        $this->setUpConverter([
            'table_of_contents' => [
                'min_heading_level' => 2,
                'max_heading_level' => 5,
            ],
        ]);

        foreach ($this->loadTests(__DIR__ . '/xml', 'min-max', '.md', '.xml') as [$markdown, $xml, $testName]) {
            $this->assertMarkdownRendersToXml($markdown, $xml, $testName);
        }
    }

    public function testHeadingsWithInlines(): void
    {
        $this->setupConverter();

        foreach ($this->loadTests(__DIR__ . '/xml', 'headings-with-inlines', '.md', '.xml') as [$markdown, $xml, $testName]) {
            $this->assertMarkdownRendersToXml($markdown, $xml, $testName);
        }
    }

    public function assertMarkdownRendersToXml(string $markdown, string $expectedXml, string $testName): void
    {
        $document = $this->parser->parse($markdown);

        $this->assertSame($expectedXml, $this->renderer->renderDocument($document)->getContent(), \sprintf('Unexpected result for "%s" test', $testName));
    }

    /**
     * @param array<string, mixed> $config
     */
    protected function setUpConverter(array $config = []): void
    {
        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new HeadingPermalinkExtension());
        $environment->addExtension(new TableOfContentsExtension());

        $this->parser   = new MarkdownParser($environment);
        $this->renderer = new XmlRenderer($environment);
    }
}
