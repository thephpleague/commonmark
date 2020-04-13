<?php

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
    /** @var MarkdownConverterInterface */
    protected $converter;

    /**
     * @param string $markdown Markdown to parse
     * @param string $html     Expected result
     * @param string $testName Name of the test
     *
     * @dataProvider dataProvider
     */
    protected function assertMarkdownRendersAs(string $markdown, string $html, string $testName)
    {
        $actualResult = $this->converter->convertToHtml($markdown);

        $failureMessage = sprintf('Unexpected result for "%s" test', $testName);
        $failureMessage .= "\n=== markdown ===============\n" . $markdown;
        $failureMessage .= "\n=== expected ===============\n" . $html;
        $failureMessage .= "\n=== got ====================\n" . $actualResult;

        $this->assertEquals($html, $actualResult, $failureMessage);
    }

    /**
     * @param string $dir
     * @param string $pattern
     *
     * @return iterable
     */
    protected function loadTests(string $dir, string $pattern = '*.md'): iterable
    {
        $finder = new Finder();
        $finder->files()
            ->in($dir)
            ->depth('== 0')
            ->name($pattern);

        /** @var SplFileInfo $markdownFile */
        foreach ($finder as $markdownFile) {
            $testName = $markdownFile->getBasename('.md');
            $markdown = $markdownFile->getContents();
            $html = file_get_contents($dir . '/' . $testName . '.html');

            yield [$markdown, $html, $testName];
        }
    }
}
