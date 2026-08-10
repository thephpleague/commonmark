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

namespace League\CommonMark\Tests\Functional\Extension\NormalizeHeadings;

use League\CommonMark\ConverterInterface;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Exception\CommonMarkException;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\NormalizeHeadings\NormalizeHeadingsExtension;
use League\CommonMark\Extension\TableOfContents\TableOfContentsExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Tests\Functional\AbstractLocalDataTestCase;
use League\Config\Exception\ConfigurationExceptionInterface;
use League\Config\Exception\InvalidConfigurationException;

final class NormalizeHeadingsExtensionTest extends AbstractLocalDataTestCase
{
    /**
     * @param array<string, mixed> $config
     */
    protected function createConverter(array $config = []): ConverterInterface
    {
        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new NormalizeHeadingsExtension());

        return new MarkdownConverter($environment);
    }

    /**
     * {@inheritDoc}
     */
    public static function dataProvider(): iterable
    {
        yield from self::loadTests(__DIR__ . '/md');
    }

    /**
     * @throws CommonMarkException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testRunsBeforeTableOfContentsAndHeadingPermalinks(): void
    {
        $environment = new Environment([
            'normalize_headings' => [
                'min_level' => 2,
                'max_level' => 2,
            ],
            'table_of_contents' => [
                'min_heading_level' => 2,
                'max_heading_level' => 2,
            ],
            'heading_permalink' => [
                'min_heading_level' => 2,
                'max_heading_level' => 2,
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new NormalizeHeadingsExtension());
        $environment->addExtension(new HeadingPermalinkExtension());
        $environment->addExtension(new TableOfContentsExtension());

        $converter = new MarkdownConverter($environment);

        $input    = '# Clamped Heading';
        $expected = '<ul class="table-of-contents">
<li><a href="#content-clamped-heading">Clamped Heading</a></li>
</ul>
<h2><a id="content-clamped-heading" href="#content-clamped-heading" class="heading-permalink" aria-hidden="true" tabindex="-1" title="Permalink">¶</a>Clamped Heading</h2>';

        $this->assertSame($expected, \trim((string) $converter->convert($input)));
    }

    /**
     * @throws CommonMarkException
     * @throws ConfigurationExceptionInterface
     */
    public function testThrowsExceptionWhenMinLevelExceedsMaxLevel(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->createConverter([
            'normalize_headings' => [
                'min_level' => 4,
                'max_level' => 2,
            ],
        ])->convert('# This should fail');
    }
}
