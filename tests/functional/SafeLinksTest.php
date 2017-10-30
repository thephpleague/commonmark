<?php

namespace League\CommonMark\Tests\Functional;

use League\CommonMark\CommonMarkConverter;
use PHPUnit\Framework\TestCase;

class SafeLinksTest extends TestCase
{
    public function testDefaultConfig()
    {
        $input = file_get_contents(__DIR__ . '/data/safe_links/input.md');
        $expectedOutput = trim(file_get_contents(__DIR__ . '/data/safe_links/unsafe_output.html'));

        $converter = new CommonMarkConverter();
        $actualOutput = trim($converter->convertToHtml($input));

        $this->assertEquals($expectedOutput, $actualOutput);
    }

    public function testSafeConfig()
    {
        $input = file_get_contents(__DIR__ . '/data/safe_links/input.md');
        $expectedOutput = trim(file_get_contents(__DIR__ . '/data/safe_links/safe_output.html'));

        $converter = new CommonMarkConverter(['allow_unsafe_links' => false]);
        $actualOutput = trim($converter->convertToHtml($input));

        $this->assertEquals($expectedOutput, $actualOutput);
    }
}
