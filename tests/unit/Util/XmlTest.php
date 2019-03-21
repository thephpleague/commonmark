<?php

namespace League\CommonMark\Tests\Unit\Util;

use League\CommonMark\Util\Xml;
use PHPUnit\Framework\TestCase;

class XmlTest extends TestCase
{
    /**
     * @param string    $input
     * @param string    $expectedOutput
     * @param bool|null $preserveEntities
     *
     * @dataProvider dataProviderForTestEscape
     */
    public function testEscape($input, $expectedOutput, $preserveEntities = null)
    {
        if ($preserveEntities === null) {
            $this->assertEquals($expectedOutput, Xml::escape($input));
        } else {
            $this->assertEquals($expectedOutput, Xml::escape($input, $preserveEntities));
        }
    }

    public function dataProviderForTestEscape()
    {
        yield ['foo', 'foo', null];

        yield ['&copy;', '&amp;copy;', null];
        yield ['&copy;', '&amp;copy;', false];
        yield ['&copy;', '&copy;', true];

        yield ['<script>', '&lt;script&gt;', null];
        yield ['<script>', '&lt;script&gt;', false];
        yield ['<script>', '&lt;script&gt;', true];

        yield ['&#x0000;', '&amp;#x0000;', null];
        yield ['&#x0000;', '&amp;#x0000;', false];
        yield ['&#x0000;', '&#x0000;', true];
    }
}
