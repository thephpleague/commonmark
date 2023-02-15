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

use League\CommonMark\ConverterInterface;
use League\CommonMark\Extension\FrontMatter\Data\SymfonyYamlFrontMatterParser;
use League\CommonMark\Extension\FrontMatter\FrontMatterParser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Tests the parser against locally-stored examples
 *
 * This is particularly useful for testing minor variations allowed by the spec
 * or small regressions not tested by the spec.
 */
abstract class AbstractLocalDataTestCase extends TestCase
{
    /**
     * @param array<string, mixed> $config
     */
    abstract protected function createConverter(array $config = []): ConverterInterface;

    /**
     * @return iterable<array{string, string, array<string, mixed>, string}>
     */
    abstract public static function dataProvider(): iterable;

    /**
     * @dataProvider dataProvider
     *
     * @param string               $markdown Markdown to parse
     * @param string               $html     Expected result
     * @param array<string, mixed> $config   Configuration loaded from front matter
     * @param string               $testName Name of the test
     */
    public function testWithLocalData(string $markdown, string $html, array $config, string $testName): void
    {
        $actualResult = (string) $this->createConverter($config)->convert($markdown);

        $failureMessage  = \sprintf('Unexpected result for "%s" test', $testName);
        $failureMessage .= "\n=== markdown ===============\n" . $markdown;
        $failureMessage .= "\n=== expected ===============\n" . $html;
        $failureMessage .= "\n=== got ====================\n" . $actualResult;

        $this->assertEquals($html, $actualResult, $failureMessage);
    }

    /**
     * @return iterable<array{string, string, array<string, mixed>, string}>
     */
    final protected static function loadTests(string $dir, string $pattern = '*', string $inputFormat = '.md', string $outputFormat = '.html'): iterable
    {
        $finder = new Finder();
        $finder->files()
            ->in($dir)
            ->depth('== 0')
            ->name($pattern . $inputFormat);

        foreach ($finder as $markdownFile) {
            \assert($markdownFile instanceof SplFileInfo);
            $testName = $markdownFile->getBasename($inputFormat);
            $input    = $markdownFile->getContents();
            $parsed   = (new FrontMatterParser(new SymfonyYamlFrontMatterParser()))->parse($input);
            $html     = \file_get_contents($dir . '/' . $testName . $outputFormat);

            yield $testName => [$parsed->getContent(), $html, (array) $parsed->getFrontMatter(), $testName];
        }
    }
}
