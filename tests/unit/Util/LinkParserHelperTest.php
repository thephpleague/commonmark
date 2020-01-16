<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Util;

use League\CommonMark\Cursor;
use League\CommonMark\Util\LinkParserHelper;
use PHPUnit\Framework\TestCase;

class LinkParserHelperTest extends TestCase
{
    /**
     * @param string $input
     * @param string $expected
     *
     * @dataProvider dataProviderForTestParseLinkDestination
     */
    public function testParseLinkDestination(string $input, string $expected)
    {
        $cursor = new Cursor($input);
        $this->assertSame($expected, LinkParserHelper::parseLinkDestination($cursor));
    }

    public function dataProviderForTestParseLinkDestination()
    {
        yield ['www.google.com', 'www.google.com'];
        yield ['<www.google.com>', 'www.google.com'];
        yield ['<www.google.com> is great', 'www.google.com'];
        yield ['\\b\\', '%5Cb%5C']; // Regression test for https://github.com/thephpleague/commonmark/issues/403
    }

    /**
     * @param string $input
     * @param int    $expected
     *
     * @dataProvider dataProviderForTestParseLinkLabel
     */
    public function testParseLinkLabel(string $input, int $expected)
    {
        $cursor = new Cursor($input);
        $this->assertSame($expected, LinkParserHelper::parseLinkLabel($cursor));
    }

    public function dataProviderForTestParseLinkLabel()
    {
        yield ['[link](http://example.com)', 6];
        yield ['[\\]: test', 0];
    }
}
