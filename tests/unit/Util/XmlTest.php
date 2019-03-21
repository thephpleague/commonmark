<?php

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
