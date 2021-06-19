<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 * (c) Rezo Zero / Ambroise Maupate
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\CommonMark\Tests\Functional\Extension\Footnote;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Footnote\FootnoteExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Tests\Functional\AbstractLocalDataTest;

/**
 * @internal
 */
final class FootnoteExtensionMarkdownTest extends AbstractLocalDataTest
{
    private MarkdownConverter $commonMarkConverter;

    private MarkdownConverter $gfmConverter;

    protected function setUp(): void
    {
        /*
         * Test with minimal extensions
         */
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new FootnoteExtension());
        $this->commonMarkConverter = new MarkdownConverter($environment);

        /*
         * Test with other extensions
         */
        $gfmEnvironment = new Environment();
        $gfmEnvironment->addExtension(new CommonMarkCoreExtension());
        $gfmEnvironment->addExtension(new GithubFlavoredMarkdownExtension());
        $gfmEnvironment->addExtension(new FootnoteExtension());
        $this->gfmConverter = new MarkdownConverter($gfmEnvironment);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testRenderer(string $markdown, string $html, string $testName): void
    {
        $this->converter = $this->commonMarkConverter;
        $this->assertMarkdownRendersAs($markdown, $html, $testName);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testExtraRenderer(string $markdown, string $html, string $testName): void
    {
        $this->converter = $this->gfmConverter;
        $this->assertMarkdownRendersAs($markdown, $html, $testName);
    }

    /**
     * @return iterable<string, string, string>
     */
    public function dataProvider(): iterable
    {
        foreach ($this->loadTests(__DIR__ . '/md') as $test) {
            yield $test;
        }
    }
}
