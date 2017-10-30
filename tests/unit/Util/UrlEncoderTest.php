<?php

namespace League\CommonMark\Tests\Unit\Util;

use League\CommonMark\Util\UrlEncoder;
use PHPUnit\Framework\TestCase;

class UrlEncoderTest extends TestCase
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
            ['https://en.wikipedia.org/wiki/Markdown#CommonMark', 'https://en.wikipedia.org/wiki/Markdown#CommonMark'],
            ['https://img.shields.io/badge/help-%23hoaproject-ff0066.svg', 'https://img.shields.io/badge/help-%23hoaproject-ff0066.svg'],
            ['http://example.com/a%62%63%2fd%3Fe', 'http://example.com/abc%2Fd%3Fe'],
            ['http://ko.wikipedia.org/wiki/위키백과:대문', 'http://ko.wikipedia.org/wiki/%EC%9C%84%ED%82%A4%EB%B0%B1%EA%B3%BC:%EB%8C%80%EB%AC%B8'],
            ['http://ko.wikipedia.org/wiki/%EC%9C%84%ED%82%A4%EB%B0%B1%EA%B3%BC:%EB%8C%80%EB%AC%B8', 'http://ko.wikipedia.org/wiki/%EC%9C%84%ED%82%A4%EB%B0%B1%EA%B3%BC:%EB%8C%80%EB%AC%B8'],
        ];
    }
}
