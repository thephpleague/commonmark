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

namespace League\CommonMark\Tests\Unit\Util;

use League\CommonMark\Util\Xml;
use PHPUnit\Framework\TestCase;

final class XmlTest extends TestCase
{
    /**
     * @dataProvider dataProviderForTestEscape
     */
    public function testEscape(string $input, string $expectedOutput): void
    {
        $this->assertEquals($expectedOutput, Xml::escape($input));
    }

    /**
     * @return iterable<string[]>
     */
    public static function dataProviderForTestEscape(): iterable
    {
        yield ['foo', 'foo'];
        yield ['&copy;', '&amp;copy;'];
        yield ['<script>', '&lt;script&gt;'];
        yield ['&#x0000;', '&amp;#x0000;'];
    }
}
