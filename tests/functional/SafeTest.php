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
use PHPUnit\Framework\TestCase;

class SafeTest extends TestCase
{
    public function testDefaultConfig()
    {
        $input = file_get_contents(__DIR__ . '/data/safe/input.md');
        $expectedOutput = trim(file_get_contents(__DIR__ . '/data/safe/unsafe_output.html'));

        $converter = new CommonMarkConverter();
        $actualOutput = trim($converter->convertToHtml($input));

        $this->assertEquals($expectedOutput, $actualOutput);
    }

    public function testSafeConfig()
    {
        $input = file_get_contents(__DIR__ . '/data/safe/input.md');
        $expectedOutput = trim(file_get_contents(__DIR__ . '/data/safe/safe_output.html'));

        $converter = new CommonMarkConverter(['safe' => true]);
        $actualOutput = trim($converter->convertToHtml($input));

        $this->assertEquals($expectedOutput, $actualOutput);
    }
}
