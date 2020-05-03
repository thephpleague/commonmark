<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Functional\Extension\TableOfContents;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\TableOfContents\TableOfContentsExtension;
use League\CommonMark\Tests\Functional\AbstractLocalDataTest;

final class TableOfContentsExtensionTest extends AbstractLocalDataTest
{
    public function testWithSampleData()
    {
        $this->setUpConverter();

        foreach ($this->loadTests(__DIR__ . '/data', 'sample.md') as [$markdown, $html, $testName]) {
            $this->assertMarkdownRendersAs($markdown, $html, $testName);
        }
    }

    public function testWithWeirdNestingLeavingItAsIs()
    {
        $this->setUpConverter([
            'table_of_contents' => [
                'normalize' => 'as-is',
            ],
        ]);

        foreach ($this->loadTests(__DIR__ . '/data', 'weird-as-is.md') as [$markdown, $html, $testName]) {
            $this->assertMarkdownRendersAs($markdown, $html, $testName);
        }
    }

    public function testWithWeirdNestingWithRelativeNormalization()
    {
        $this->setUpConverter([
            'table_of_contents' => [
                'normalize' => 'relative',
            ],
        ]);

        foreach ($this->loadTests(__DIR__ . '/data', 'weird-relative.md') as [$markdown, $html, $testName]) {
            $this->assertMarkdownRendersAs($markdown, $html, $testName);
        }
    }

    public function testWithWeirdNestingWithFlattenedNormalization()
    {
        $this->setUpConverter([
            'table_of_contents' => [
                'normalize' => 'flat',
            ],
        ]);

        foreach ($this->loadTests(__DIR__ . '/data', 'weird-flattened.md') as [$markdown, $html, $testName]) {
            $this->assertMarkdownRendersAs($markdown, $html, $testName);
        }
    }

    public function testWithPositionTop()
    {
        $this->setUpConverter([
            'table_of_contents' => [
                'position' => 'top',
            ],
        ]);

        foreach ($this->loadTests(__DIR__ . '/data', 'position-top.md') as [$markdown, $html, $testName]) {
            $this->assertMarkdownRendersAs($markdown, $html, $testName);
        }
    }

    public function testWithPositionBeforeHeadings()
    {
        $this->setUpConverter([
            'table_of_contents' => [
                'position' => 'before-headings',
            ],
        ]);

        foreach ($this->loadTests(__DIR__ . '/data', 'position-before-headings.md') as [$markdown, $html, $testName]) {
            $this->assertMarkdownRendersAs($markdown, $html, $testName);
        }
    }

    public function testWithPositionPlaceholder()
    {
        $this->setUpConverter([
            'table_of_contents' => [
                'position'    => 'placeholder',
                'placeholder' => '[TOC]',
            ],
        ]);

        foreach ($this->loadTests(__DIR__ . '/data', 'position-placeholder*.md') as [$markdown, $html, $testName]) {
            $this->assertMarkdownRendersAs($markdown, $html, $testName);
        }
    }

    public function testWithCustomClass()
    {
        $this->setUpConverter([
            'table_of_contents' => [
                'html_class' => 'markdown-toc',
            ],
        ]);

        foreach ($this->loadTests(__DIR__ . '/data', 'custom-class.md') as [$markdown, $html, $testName]) {
            $this->assertMarkdownRendersAs($markdown, $html, $testName);
        }
    }

    public function testWithBulletedStyle()
    {
        $this->setUpConverter([
            'table_of_contents' => [
                'style' => 'bullet',
            ],
        ]);

        foreach ($this->loadTests(__DIR__ . '/data', 'style-bullet.md') as [$markdown, $html, $testName]) {
            $this->assertMarkdownRendersAs($markdown, $html, $testName);
        }
    }

    public function testWithOrderedStyle()
    {
        $this->setUpConverter([
            'table_of_contents' => [
                'style' => 'ordered',
            ],
        ]);

        foreach ($this->loadTests(__DIR__ . '/data', 'style-ordered.md') as [$markdown, $html, $testName]) {
            $this->assertMarkdownRendersAs($markdown, $html, $testName);
        }
    }

    public function testWithNoHeadings()
    {
        $this->setUpConverter();

        foreach ($this->loadTests(__DIR__ . '/data', 'no-headings.md') as [$markdown, $html, $testName]) {
            $this->assertMarkdownRendersAs($markdown, $html, $testName);
        }
    }

    public function testMinMaxHeadingLevels()
    {
        $this->setUpConverter([
            'table_of_contents' => [
                'min_heading_level' => 2,
                'max_heading_level' => 5,
            ],
        ]);

        foreach ($this->loadTests(__DIR__ . '/data', 'min-max.md') as [$markdown, $html, $testName]) {
            $this->assertMarkdownRendersAs($markdown, $html, $testName);
        }
    }

    protected function setUpConverter(array $config = [])
    {
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new HeadingPermalinkExtension());
        $environment->addExtension(new TableOfContentsExtension());

        $this->converter = new CommonMarkConverter($config, $environment);
    }
}
