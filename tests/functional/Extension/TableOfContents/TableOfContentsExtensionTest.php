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
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Tests\Functional\AbstractLocalDataTest;

final class TableOfContentsExtensionTest extends AbstractLocalDataTest
{
    public function testWithSampleData(): void
    {
        $this->setUpConverter();

        foreach ($this->loadTests(__DIR__ . '/md', 'sample') as [$markdown, $html, $testName]) {
            $this->assertMarkdownRendersAs($markdown, $html, $testName);
        }
    }

    public function testWithHtmlInHeadings(): void
    {
        $this->setUpConverter();

        foreach ($this->loadTests(__DIR__ . '/md', 'html-in-headings') as [$markdown, $html, $testName]) {
            $this->assertMarkdownRendersAs($markdown, $html, $testName);
        }
    }

    public function testWithWeirdNestingLeavingItAsIs(): void
    {
        $this->setUpConverter([
            'table_of_contents' => [
                'normalize' => 'as-is',
            ],
        ]);

        foreach ($this->loadTests(__DIR__ . '/md', 'weird-as-is') as [$markdown, $html, $testName]) {
            $this->assertMarkdownRendersAs($markdown, $html, $testName);
        }
    }

    public function testWithWeirdNestingWithRelativeNormalization(): void
    {
        $this->setUpConverter([
            'table_of_contents' => [
                'normalize' => 'relative',
            ],
        ]);

        foreach ($this->loadTests(__DIR__ . '/md', 'weird-relative') as [$markdown, $html, $testName]) {
            $this->assertMarkdownRendersAs($markdown, $html, $testName);
        }
    }

    public function testWithWeirdNestingWithFlattenedNormalization(): void
    {
        $this->setUpConverter([
            'table_of_contents' => [
                'normalize' => 'flat',
            ],
        ]);

        foreach ($this->loadTests(__DIR__ . '/md', 'weird-flattened') as [$markdown, $html, $testName]) {
            $this->assertMarkdownRendersAs($markdown, $html, $testName);
        }
    }

    public function testWithPositionTop(): void
    {
        $this->setUpConverter([
            'table_of_contents' => [
                'position' => 'top',
            ],
        ]);

        foreach ($this->loadTests(__DIR__ . '/md', 'position-top') as [$markdown, $html, $testName]) {
            $this->assertMarkdownRendersAs($markdown, $html, $testName);
        }
    }

    public function testWithPositionBeforeHeadings(): void
    {
        $this->setUpConverter([
            'table_of_contents' => [
                'position' => 'before-headings',
            ],
        ]);

        foreach ($this->loadTests(__DIR__ . '/md', 'position-before-headings') as [$markdown, $html, $testName]) {
            $this->assertMarkdownRendersAs($markdown, $html, $testName);
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

        foreach ($this->loadTests(__DIR__ . '/md', 'position-placeholder*') as [$markdown, $html, $testName]) {
            $this->assertMarkdownRendersAs($markdown, $html, $testName);
        }
    }

    public function testWithCustomClass(): void
    {
        $this->setUpConverter([
            'table_of_contents' => [
                'html_class' => 'markdown-toc',
            ],
        ]);

        foreach ($this->loadTests(__DIR__ . '/md', 'custom-class') as [$markdown, $html, $testName]) {
            $this->assertMarkdownRendersAs($markdown, $html, $testName);
        }
    }

    public function testWithBulletedStyle(): void
    {
        $this->setUpConverter([
            'table_of_contents' => [
                'style' => 'bullet',
            ],
        ]);

        foreach ($this->loadTests(__DIR__ . '/md', 'style-bullet') as [$markdown, $html, $testName]) {
            $this->assertMarkdownRendersAs($markdown, $html, $testName);
        }
    }

    public function testWithOrderedStyle(): void
    {
        $this->setUpConverter([
            'table_of_contents' => [
                'style' => 'ordered',
            ],
        ]);

        foreach ($this->loadTests(__DIR__ . '/md', 'style-ordered') as [$markdown, $html, $testName]) {
            $this->assertMarkdownRendersAs($markdown, $html, $testName);
        }
    }

    public function testWithNoHeadings(): void
    {
        $this->setUpConverter();

        foreach ($this->loadTests(__DIR__ . '/md', 'no-headings') as [$markdown, $html, $testName]) {
            $this->assertMarkdownRendersAs($markdown, $html, $testName);
        }
    }

    public function testWithSetextHeadings(): void
    {
        $this->setUpConverter();

        foreach ($this->loadTests(__DIR__ . '/md', 'setext-headings') as [$markdown, $html, $testName]) {
            $this->assertMarkdownRendersAs($markdown, $html, $testName);
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

        foreach ($this->loadTests(__DIR__ . '/md', 'min-max') as [$markdown, $html, $testName]) {
            $this->assertMarkdownRendersAs($markdown, $html, $testName);
        }
    }

    public function testHeadingsWithInlines(): void
    {
        $this->setupConverter();

        foreach ($this->loadTests(__DIR__ . '/md', 'headings-with-inlines') as [$markdown, $html, $testName]) {
            $this->assertMarkdownRendersAs($markdown, $html, $testName);
        }
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

        $this->converter = new MarkdownConverter($environment);
    }
}
