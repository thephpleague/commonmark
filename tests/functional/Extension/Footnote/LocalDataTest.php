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

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\Footnote\FootnoteExtension;
use League\CommonMark\Tests\Functional\AbstractLocalDataTest;

/**
 * @internal
 */
final class LocalDataTest extends AbstractLocalDataTest
{
    /** @var CommonMarkConverter */
    private $commonMarkConverter;

    /** @var CommonMarkConverter */
    private $gfmConverter;

    protected function setUp(): void
    {
        /*
         * Test with minimal extensions
         */
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new FootnoteExtension());
        $this->commonMarkConverter = new CommonMarkConverter([], $environment);

        /*
         * Test with other extensions
         */
        $gfmEnvironment = Environment::createGFMEnvironment();
        $gfmEnvironment->addExtension(new FootnoteExtension());
        $this->gfmConverter = new CommonMarkConverter([], $gfmEnvironment);
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
        foreach ($this->loadTests(__DIR__ . '/data', '*.md') as $test) {
            yield $test;
        }
    }
}
