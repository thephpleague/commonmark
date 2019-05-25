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

namespace League\CommonMark\Tests\Unit\Util;

use League\CommonMark\Util\Xml;
use PHPUnit\Framework\TestCase;

class XmlTest extends TestCase
{
    /**
     * @param string $input
     * @param string $expectedOutput
     *
     * @dataProvider dataProviderForTestEscape
     */
    public function testEscape($input, $expectedOutput)
    {
        $this->assertEquals($expectedOutput, Xml::escape($input));
    }

    public function dataProviderForTestEscape()
    {
        yield ['foo', 'foo'];
        yield ['&copy;', '&amp;copy;'];
        yield ['<script>', '&lt;script&gt;'];
        yield ['&#x0000;', '&amp;#x0000;'];
    }
}
