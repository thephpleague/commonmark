<?php

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
use League\CommonMark\Environment;
use PHPUnit\Framework\TestCase;

class HtmlInputTest extends TestCase
{
    public function testDefaultConfig()
    {
        $input = file_get_contents(__DIR__ . '/data/html_input/input.md');
        $expectedOutput = trim(file_get_contents(__DIR__ . '/data/html_input/unsafe_output.html'));

        $converter = new CommonMarkConverter();
        $actualOutput = trim($converter->convertToHtml($input));

        $this->assertEquals($expectedOutput, $actualOutput);
    }

    public function testAllowHtmlInputConfig()
    {
        $input = file_get_contents(__DIR__ . '/data/html_input/input.md');
        $expectedOutput = trim(file_get_contents(__DIR__ . '/data/html_input/unsafe_output.html'));

        $converter = new CommonMarkConverter(['html_input' => Environment::HTML_INPUT_ALLOW]);
        $actualOutput = trim($converter->convertToHtml($input));

        $this->assertEquals($expectedOutput, $actualOutput);
    }

    public function testEscapeHtmlInputConfig()
    {
        $input = file_get_contents(__DIR__ . '/data/html_input/input.md');
        $expectedOutput = trim(file_get_contents(__DIR__ . '/data/html_input/escaped_output.html'));

        $converter = new CommonMarkConverter(['html_input' => Environment::HTML_INPUT_ESCAPE]);
        $actualOutput = trim($converter->convertToHtml($input));

        $this->assertEquals($expectedOutput, $actualOutput);
    }

    public function testStripHtmlInputConfig()
    {
        $input = file_get_contents(__DIR__ . '/data/html_input/input.md');
        $expectedOutput = trim(file_get_contents(__DIR__ . '/data/html_input/safe_output.html'));

        $converter = new CommonMarkConverter(['html_input' => Environment::HTML_INPUT_STRIP]);
        $actualOutput = trim($converter->convertToHtml($input));

        $this->assertEquals($expectedOutput, $actualOutput);
    }
}
