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
use PHPUnit\Framework\TestCase;

final class SafeLinksTest extends TestCase
{
    public function testDefaultConfig(): void
    {
        $input          = \file_get_contents(__DIR__ . '/data/safe_links/input.md');
        $expectedOutput = \trim(\file_get_contents(__DIR__ . '/data/safe_links/unsafe_output.html'));

        $converter    = new CommonMarkConverter();
        $actualOutput = \trim((string) $converter->convert($input));

        $this->assertEquals($expectedOutput, $actualOutput);
    }

    public function testSafeConfig(): void
    {
        $input          = \file_get_contents(__DIR__ . '/data/safe_links/input.md');
        $expectedOutput = \trim(\file_get_contents(__DIR__ . '/data/safe_links/safe_output.html'));

        $converter    = new CommonMarkConverter(['allow_unsafe_links' => false]);
        $actualOutput = \trim((string) $converter->convert($input));

        $this->assertEquals($expectedOutput, $actualOutput);
    }
}
