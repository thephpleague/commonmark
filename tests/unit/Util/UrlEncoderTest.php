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

use League\CommonMark\Exception\UnexpectedEncodingException;
use League\CommonMark\Util\UrlEncoder;
use PHPUnit\Framework\TestCase;

final class UrlEncoderTest extends TestCase
{
    /**
     * @dataProvider unescapeAndEncodeTestProvider
     */
    public function testUnescapeAndEncode(string $input, string $expected): void
    {
        $this->assertEquals($expected, UrlEncoder::unescapeAndEncode($input));
    }

    /**
     * @return iterable<array<string>>
     */
    public static function unescapeAndEncodeTestProvider(): iterable
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
            ['%21', '%21'],
            ['%23', '%23'],
            ['%24', '%24'],
            ['%26', '%26'],
            ['%27', '%27'],
            ['%2A', '%2A'],
            ['%2B', '%2B'],
            ['%2C', '%2C'],
            ['%2D', '%2D'],
            ['%2E', '%2E'],
            ['%2F', '%2F'],
            ['%3A', '%3A'],
            ['%3B', '%3B'],
            ['%3D', '%3D'],
            ['%3F', '%3F'],
            ['%40', '%40'],
            ['%5F', '%5F'],
            ['%7E', '%7E'],
            ['%ED', '%ED'],
            ['java%0ascript:alert("XSS")', 'java%0ascript:alert(%22XSS%22)'],
            ['java%0Ascript:alert("XSS")', 'java%0Ascript:alert(%22XSS%22)'],
            ["java\nscript:alert('XSS')", "java%0Ascript:alert('XSS')"],
            ['javascript&amp;colon;alert%28&#039;XSS&#039;%29', 'javascript&amp;colon;alert%28&#039;XSS&#039;%29'],
            ['https://en.wikipedia.org/wiki/Markdown#CommonMark', 'https://en.wikipedia.org/wiki/Markdown#CommonMark'],
            ['https://img.shields.io/badge/help-%23hoaproject-ff0066.svg', 'https://img.shields.io/badge/help-%23hoaproject-ff0066.svg'],
            ['http://example.com/a%62%63%2fd%3Fe', 'http://example.com/a%62%63%2fd%3Fe'],
            ['http://ko.wikipedia.org/wiki/위키백과:대문', 'http://ko.wikipedia.org/wiki/%EC%9C%84%ED%82%A4%EB%B0%B1%EA%B3%BC:%EB%8C%80%EB%AC%B8'],
            ['http://ko.wikipedia.org/wiki/%EC%9C%84%ED%82%A4%EB%B0%B1%EA%B3%BC:%EB%8C%80%EB%AC%B8', 'http://ko.wikipedia.org/wiki/%EC%9C%84%ED%82%A4%EB%B0%B1%EA%B3%BC:%EB%8C%80%EB%AC%B8'],
            ['http://ko.wikipedia.org/wiki/%EC%9C%84%ED%82%A4%EB%B0%B1%EA%B3%BC:%EB%8C%80%EB%AC%B8', 'http://ko.wikipedia.org/wiki/%EC%9C%84%ED%82%A4%EB%B0%B1%EA%B3%BC:%EB%8C%80%EB%AC%B8'],
            ['http://www.inpec.gov.co/portal/page/portal/Inpec/Institucion/Estad%EDsticas/Estadisticas/Informes%20y%20Boletines%20Estad%EDsticos/1%20INFORME%20ENERO%202017.pdf', 'http://www.inpec.gov.co/portal/page/portal/Inpec/Institucion/Estad%EDsticas/Estadisticas/Informes%20y%20Boletines%20Estad%EDsticos/1%20INFORME%20ENERO%202017.pdf'],
            ['https://www.example.com/stocks-rise-50%-today.html', 'https://www.example.com/stocks-rise-50%25-today.html'],
            ['data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUAAAAFCAYAAACNbyblAAAAHElEQVQI12P4//8/w38GIAXDIBKE0DHxgljNBAAO9TXL0Y4OHwAAAABJRU5ErkJggg==', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUAAAAFCAYAAACNbyblAAAAHElEQVQI12P4//8/w38GIAXDIBKE0DHxgljNBAAO9TXL0Y4OHwAAAABJRU5ErkJggg=='],
            ['data:image/png;base64, iVBORw0KGgoAAAANSUhEUgAAAAUAAAAFCAYAAACNbyblAAAAHElEQVQI12P4//8/w38GIAXDIBKE0DHxgljNBAAO9TXL0Y4OHwAAAABJRU5ErkJggg==', 'data:image/png;base64,%20iVBORw0KGgoAAAANSUhEUgAAAAUAAAAFCAYAAACNbyblAAAAHElEQVQI12P4//8/w38GIAXDIBKE0DHxgljNBAAO9TXL0Y4OHwAAAABJRU5ErkJggg=='],
        ];
    }

    public function testInvalidUnicodeProducesAnException(): void
    {
        $this->expectException(UnexpectedEncodingException::class);

        UrlEncoder::unescapeAndEncode(\hex2bin('A5A5A5'));
    }
}
