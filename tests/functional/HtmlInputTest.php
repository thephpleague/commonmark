<?php

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
