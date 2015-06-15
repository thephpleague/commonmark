<?php

namespace League\CommonMark\Tests\Unit\Util;

use League\CommonMark\Util\UrlEncoder;

class UrlEncoderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider unescapeAndEncodeTestProvider
     */
    public function testUnescapeAndEncode($input, $expected)
    {
        $this->assertEquals($expected, UrlEncoder::unescapeAndEncode($input));
    }

    public function unescapeAndEncodeTestProvider()
    {
        return [
            ['(foo)', '(foo)'],
            ['/my uri', '/my%20uri'],
            ['`', '%60'],
            ['~', '~'],
            ['!', '!'],
            ['@', '@'],
            ['#', '#'],
            ['$', '$'],
            ['%', '%25'],
            ['^', '%5E'],
            ['&', '&'],
            ['*', '*'],
            ['(', '('],
            [')', ')'],
            ['-', '-'],
            ['_', '_'],
            ['=', '='],
            ['+', '+'],
            ['{', '%7B'],
            ['}', '%7D'],
            ['[', '%5B'],
            [']', '%5D'],
            ['\\', '%5C'],
            ['|', '%7C'],
            [';', ';'],
            ['\'', '\''],
            [':', ':'],
            ['"', '%22'],
            [',', ','],
            ['.', '.'],
            ['/', '/'],
            ['<', '%3C'],
            ['>', '%3E'],
            ['?', '?'],
        ];
    }
}
