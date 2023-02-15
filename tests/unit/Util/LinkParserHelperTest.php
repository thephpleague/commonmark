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

use League\CommonMark\Parser\Cursor;
use League\CommonMark\Util\LinkParserHelper;
use PHPUnit\Framework\TestCase;

final class LinkParserHelperTest extends TestCase
{
    /**
     * @dataProvider dataProviderForTestParseLinkDestination
     */
    public function testParseLinkDestination(string $input, string $expected): void
    {
        $cursor = new Cursor($input);
        $this->assertSame($expected, LinkParserHelper::parseLinkDestination($cursor));
    }

    /**
     * @return iterable<array<string>>
     */
    public static function dataProviderForTestParseLinkDestination(): iterable
    {
        yield ['www.google.com', 'www.google.com'];
        yield ['<www.google.com>', 'www.google.com'];
        yield ['<www.google.com> is great', 'www.google.com'];
        yield ['\\b\\', '%5Cb%5C']; // Regression test for https://github.com/thephpleague/commonmark/issues/403
    }

    /**
     * @dataProvider dataProviderForTestParseLinkLabel
     */
    public function testParseLinkLabel(string $input, int $expected): void
    {
        $cursor = new Cursor($input);
        $this->assertSame($expected, LinkParserHelper::parseLinkLabel($cursor));
    }

    /**
     * @return iterable<array<mixed>>
     */
    public static function dataProviderForTestParseLinkLabel(): iterable
    {
        yield ['[link](http://example.com)', 6];
        yield ['[\\]: test', 0];
    }
}
