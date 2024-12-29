<?php

declare(strict_types=1);

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

use League\CommonMark\Environment\Environment;
use League\CommonMark\Exception\InvalidArgumentException;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;
use PHPUnit\Framework\TestCase;

final class DelimiterProcessingTest extends TestCase
{
    public function testDelimiterProcessorWithInvalidDelimiterUse(): void
    {
        $e = new Environment();
        $e->addExtension(new CommonMarkCoreExtension());
        $e->addDelimiterProcessor(new FakeDelimiterProcessor(':', 0));
        $e->addDelimiterProcessor(new FakeDelimiterProcessor(';', -1));

        $c = new MarkdownConverter($e);

        $this->assertEquals("<p>:test:</p>\n", $c->convert(':test:'));
        $this->assertEquals("<p>;test;</p>\n", $c->convert(';test;'));
    }

    /**
     * @dataProvider asymmetricDelimiterDataProvider
     */
    public function testAsymmetricDelimiterProcessing(string $input, string $expected): void
    {
        $e = new Environment();
        $e->addExtension(new CommonMarkCoreExtension());
        $e->addDelimiterProcessor(new UppercaseDelimiterProcessor());
        $e->addRenderer(UppercaseText::class, new UppercaseTextRenderer());

        $converter = new MarkdownConverter($e);

        $this->assertEquals($expected, $converter->convert($input)->getContent());
    }

    /**
     * @return iterable<array<string>>
     */
    public static function asymmetricDelimiterDataProvider(): iterable
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

    public function testMultipleDelimitersWithDifferentLengths(): void
    {
        $e = new Environment();
        $e->addExtension(new CommonMarkCoreExtension());
        $e->addDelimiterProcessor(new TestDelimiterProcessor('@', 1));
        $e->addDelimiterProcessor(new TestDelimiterProcessor('@', 2));

        $c = new MarkdownConverter($e);

        $this->assertEquals("<p>(1)one(/1) (2)two(/2)</p>\n", $c->convert('@one@ @@two@@'));
        $this->assertEquals("<p>(1)(2)both(/2)(/1)</p>\n", $c->convert('@@@both@@@'));
    }

    public function testMultipleDelimitersWithSameLength(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $e = new Environment();
        $e->addExtension(new CommonMarkCoreExtension());
        $e->addDelimiterProcessor(new TestDelimiterProcessor('@', 1));
        $e->addDelimiterProcessor(new TestDelimiterProcessor('@', 1));
    }
}
