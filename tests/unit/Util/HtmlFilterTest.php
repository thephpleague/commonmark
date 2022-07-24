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

namespace League\CommonMark\Tests\Unit\Util;

use League\CommonMark\Exception\InvalidArgumentException;
use League\CommonMark\Util\HtmlFilter;
use PHPUnit\Framework\TestCase;

final class HtmlFilterTest extends TestCase
{
    public function testFilterAllow(): void
    {
        $html     = 'This is a test of <script>alert("XSS")</script>';
        $expected = $html;

        $this->assertSame($expected, HtmlFilter::filter($html, HtmlFilter::ALLOW));
    }

    public function testFilterStrip(): void
    {
        $html     = 'This is a test of <script>alert("XSS")</script>!';
        $expected = '';

        $this->assertSame($expected, HtmlFilter::filter($html, HtmlFilter::STRIP));
    }

    public function testFilterEscape(): void
    {
        $html     = 'This is a test of <script>alert("XSS")</script>';
        $expected = 'This is a test of &lt;script&gt;alert("XSS")&lt;/script&gt;';

        $this->assertSame($expected, HtmlFilter::filter($html, HtmlFilter::ESCAPE));
    }

    public function testInvalidFilter(): void
    {
        $this->expectException(InvalidArgumentException::class);

        HtmlFilter::filter('', 'some-made-up-option');
    }
}
