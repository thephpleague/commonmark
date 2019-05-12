<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Additional emphasis processing code based on commonmark-java (https://github.com/atlassian/commonmark-java)
 *  - (c) Atlassian Pty Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Functional\Delimiter;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use PHPUnit\Framework\TestCase;

final class DelimiterProcessingTest extends TestCase
{
    public function testDelimiterProcessorWithInvalidDelimiterUse()
    {
        $e = Environment::createCommonMarkEnvironment();
        $e->addDelimiterProcessor(new FakeDelimiterProcessor(':', 0));
        $e->addDelimiterProcessor(new FakeDelimiterProcessor(';', -1));

        $c = new CommonMarkConverter([], $e);

        $this->assertEquals("<p>:test:</p>\n", $c->convertToHtml(':test:'));
        $this->assertEquals("<p>;test;</p>\n", $c->convertToHtml(';test;'));
    }

    /**
     * @dataProvider asymmetricDelimiterDataProvider
     */
    public function testAsymmetricDelimiterProcessing(string $input, string $expected)
    {
        $e = Environment::createCommonMarkEnvironment();
        $e->addDelimiterProcessor(new UppercaseDelimiterProcessor());
        $e->addInlineRenderer(UppercaseText::class, new UppercaseTextRenderer());

        $converter = new CommonMarkConverter([], $e);

        $this->assertEquals($expected, $converter->convertToHtml($input));
    }

    public function asymmetricDelimiterDataProvider()
    {
        yield ['{foo} bar', "<p>FOO bar</p>\n"];
        yield ['f{oo ba}r', "<p>fOO BAr</p>\n"];
        yield ['{{foo} bar', "<p>{FOO bar</p>\n"];
        yield ['{foo}} bar', "<p>FOO} bar</p>\n"];
        yield ['{{foo} bar}', "<p>FOO BAR</p>\n"];
        yield ['{foo bar', "<p>{foo bar</p>\n"];
        yield ['foo} bar', "<p>foo} bar</p>\n"];
        yield ['}foo} bar', "<p>}foo} bar</p>\n"];
        yield ['{foo{ bar', "<p>{foo{ bar</p>\n"];
        yield ['}foo{ bar', "<p>}foo{ bar</p>\n"];
        yield ['{} {foo}', "<p> FOO</p>\n"];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMultipleDelimiters()
    {
        $e = Environment::createCommonMarkEnvironment();
        $e->addDelimiterProcessor(new TestDelimiterProcessor('@'));
        $e->addDelimiterProcessor(new TestDelimiterProcessor('@'));
    }
}
