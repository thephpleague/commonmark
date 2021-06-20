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

namespace League\CommonMark\Tests\Functional;

use League\CommonMark\MarkdownConverterInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Tests the parser against locally-stored examples
 *
 * This is particularly useful for testing minor variations allowed by the spec
 * or small regressions not tested by the spec.
 */
abstract class AbstractLocalDataTest extends TestCase
{
    protected MarkdownConverterInterface $converter;

    /**
     * @dataProvider dataProvider
     *
     * @param string $markdown Markdown to parse
     * @param string $html     Expected result
     * @param string $testName Name of the test
     */
    protected function assertMarkdownRendersAs(string $markdown, string $html, string $testName): void
    {
        $actualResult = (string) $this->converter->convertToHtml($markdown);

        $failureMessage  = \sprintf('Unexpected result for "%s" test', $testName);
        $failureMessage .= "\n=== markdown ===============\n" . $markdown;
        $failureMessage .= "\n=== expected ===============\n" . $html;
        $failureMessage .= "\n=== got ====================\n" . $actualResult;

        $this->assertEquals($html, $actualResult, $failureMessage);
    }

    /**
     * @return iterable<array<string>>
     */
    protected function loadTests(string $dir, string $pattern = '*', string $inputFormat = '.md', string $outputFormat = '.html'): iterable
    {
        $finder = new Finder();
        $finder->files()
            ->in($dir)
            ->depth('== 0')
            ->name($pattern . $inputFormat);

        foreach ($finder as $markdownFile) {
            \assert($markdownFile instanceof SplFileInfo);
            $testName = $markdownFile->getBasename($inputFormat);
            $markdown = $markdownFile->getContents();
            $html     = \file_get_contents($dir . '/' . $testName . $outputFormat);

            yield [$markdown, $html, $testName];
        }
    }
}
