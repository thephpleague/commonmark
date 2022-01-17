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
use League\CommonMark\Util\HtmlFilter;
use PHPUnit\Framework\TestCase;

final class HtmlInputTest extends TestCase
{
    public function testDefaultConfig(): void
    {
        $input          = \file_get_contents(__DIR__ . '/data/html_input/input.md');
        $expectedOutput = \trim(\file_get_contents(__DIR__ . '/data/html_input/unsafe_output.html'));

        $converter    = new CommonMarkConverter();
        $actualOutput = \trim((string) $converter->convert($input));

        $this->assertEquals($expectedOutput, $actualOutput);
    }

    public function testAllowHtmlInputConfig(): void
    {
        $input          = \file_get_contents(__DIR__ . '/data/html_input/input.md');
        $expectedOutput = \trim(\file_get_contents(__DIR__ . '/data/html_input/unsafe_output.html'));

        $converter    = new CommonMarkConverter(['html_input' => HtmlFilter::ALLOW]);
        $actualOutput = \trim((string) $converter->convert($input));

        $this->assertEquals($expectedOutput, $actualOutput);
    }

    public function testEscapeHtmlInputConfig(): void
    {
        $input          = \file_get_contents(__DIR__ . '/data/html_input/input.md');
        $expectedOutput = \trim(\file_get_contents(__DIR__ . '/data/html_input/escaped_output.html'));

        $converter    = new CommonMarkConverter(['html_input' => HtmlFilter::ESCAPE]);
        $actualOutput = \trim((string) $converter->convert($input));

        $this->assertEquals($expectedOutput, $actualOutput);
    }

    public function testStripHtmlInputConfig(): void
    {
        $input          = \file_get_contents(__DIR__ . '/data/html_input/input.md');
        $expectedOutput = \trim(\file_get_contents(__DIR__ . '/data/html_input/safe_output.html'));

        $converter    = new CommonMarkConverter(['html_input' => HtmlFilter::STRIP]);
        $actualOutput = \trim((string) $converter->convert($input));

        $this->assertEquals($expectedOutput, $actualOutput);
    }
}
