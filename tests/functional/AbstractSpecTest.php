<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Functional;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Util\SpecReader;
use PHPUnit\Framework\TestCase;

abstract class AbstractSpecTest extends TestCase
{
    protected MarkdownConverter $converter;

    protected function setUp(): void
    {
        $this->converter = new CommonMarkConverter();
    }

    /**
     * @dataProvider dataProvider
     *
     * @param string $markdown Markdown to parse
     * @param string $html     Expected result
     */
    public function testSpecExample(string $markdown, string $html): void
    {
        $actualResult = (string) $this->converter->convert($markdown);

        $failureMessage  = 'Unexpected result:';
        $failureMessage .= "\n=== markdown ===============\n" . $this->showSpaces($markdown);
        $failureMessage .= "\n=== expected ===============\n" . $this->showSpaces($html);
        $failureMessage .= "\n=== got ====================\n" . $this->showSpaces($actualResult);

        $this->assertEquals($html, $actualResult, $failureMessage);
    }

    public function dataProvider(): \Generator
    {
        yield from $this->loadSpecExamples();
    }

    protected function loadSpecExamples(): \Generator
    {
        yield from SpecReader::readFile($this->getFileName());
    }

    private function showSpaces(string $str): string
    {
        return \strtr($str, ["\t" => '→', ' ' => '␣']);
    }

    abstract protected function getFileName(): string;
}
