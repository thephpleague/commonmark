<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 * (c) 2015 Martin Hasoň <martin.hason@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\CommonMark\Tests\Unit\Extension\Attributes\Util;

use League\CommonMark\Extension\Attributes\Util\AttributesHelper;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Node\Inline\AbstractInline;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Tests\Unit\Environment\FakeBlock1;
use League\CommonMark\Tests\Unit\Environment\FakeInline1;
use PHPUnit\Framework\TestCase;

final class AttributesHelperTest extends TestCase
{
    /**
     * @dataProvider dataForTestParseAttributes
     *
     * @param array<string, mixed> $expectedResult
     */
    public function testParseAttributes(Cursor $input, array $expectedResult, string $expectedRemainder = ''): void
    {
        $this->assertSame($expectedResult, AttributesHelper::parseAttributes($input));
        $this->assertSame($expectedRemainder, $input->getRemainder());
    }

    /**
     * @return iterable<Cursor|array<string, mixed>>
     */
    public static function dataForTestParseAttributes(): iterable
    {
        yield [new Cursor(''), [], ''];
        yield [new Cursor('{}'), [], '{}'];
        yield [new Cursor('{ }'), [], '{ }'];

        // Examples with colons
        yield [new Cursor('{:title="My Title"}'), ['title' => 'My Title']];
        yield [new Cursor('{: title="My Title"}'), ['title' => 'My Title']];
        yield [new Cursor('{:title="My Title" }'), ['title' => 'My Title']];
        yield [new Cursor('{: title="My Title" }'), ['title' => 'My Title']];
        yield [new Cursor('{:   title="My Title"  }'), ['title' => 'My Title']];
        yield [new Cursor('{: #custom-id }'), ['id' => 'custom-id']];
        yield [new Cursor('{: #custom-id #another-id }'), ['id' => 'another-id']];
        yield [new Cursor('{: .class1 .class2 }'), ['class' => 'class1 class2']];
        yield [new Cursor('{: #custom-id .class1 .class2 title="My Title" disabled=true }'), ['id' => 'custom-id', 'class' => 'class1 class2', 'title' => 'My Title', 'disabled' => true]];
        yield [new Cursor('{: #custom-id .class1 .class2 title="My Title" disabled="disabled" }'), ['id' => 'custom-id', 'class' => 'class1 class2', 'title' => 'My Title', 'disabled' => 'disabled']];
        yield [new Cursor('{:target=_blank}'), ['target' => '_blank']];
        yield [new Cursor('{: target=_blank}'), ['target' => '_blank']];
        yield [new Cursor('{: target=_blank }'), ['target' => '_blank']];
        yield [new Cursor('{:   target=_blank   }'), ['target' => '_blank']];
        yield [new Cursor('{: disabled=disabled}'), ['disabled' => 'disabled']];

        // Examples without colons
        yield [new Cursor('{title="My Title"}'), ['title' => 'My Title']];
        yield [new Cursor('{ title="My Title"}'), ['title' => 'My Title']];
        yield [new Cursor('{title="My Title" }'), ['title' => 'My Title']];
        yield [new Cursor('{ title="My Title" }'), ['title' => 'My Title']];
        yield [new Cursor('{   title="My Title"  }'), ['title' => 'My Title']];
        yield [new Cursor('{ #custom-id }'), ['id' => 'custom-id']];
        yield [new Cursor('{ #custom-id #another-id }'), ['id' => 'another-id']];
        yield [new Cursor('{ .class1 .class2 }'), ['class' => 'class1 class2']];
        yield [new Cursor('{ #custom-id .class1 .class2 title="My Title" disabled=true }'), ['id' => 'custom-id', 'class' => 'class1 class2', 'title' => 'My Title', 'disabled' => true]];
        yield [new Cursor('{ #custom-id .class1 .class2 title="My Title" disabled="disabled" }'), ['id' => 'custom-id', 'class' => 'class1 class2', 'title' => 'My Title', 'disabled' => 'disabled']];
        yield [new Cursor('{target=_blank}'), ['target' => '_blank']];
        yield [new Cursor('{ target=_blank}'), ['target' => '_blank']];
        yield [new Cursor('{target=_blank }'), ['target' => '_blank']];
        yield [new Cursor('{   target=_blank   }'), ['target' => '_blank']];
        yield [new Cursor('{disabled=disabled}'), ['disabled' => 'disabled']];

        // Stuff at the beginning
        yield [new Cursor(' {: #custom-id }'), ['id' => 'custom-id']];
        yield [new Cursor('  {: #custom-id }'), ['id' => 'custom-id']];
        yield [new Cursor('   {: #custom-id }'), ['id' => 'custom-id']];

        // Note that this method doesn't enforce indentation rules - that should be checked elsewhere
        yield [new Cursor('    {: #custom-id }'), ['id' => 'custom-id']];
        yield [new Cursor('      {: #custom-id }'), ['id' => 'custom-id']];

        // Stuff on the end
        yield [new Cursor('{: #custom-id } '), ['id' => 'custom-id'], ' '];

        // Note that this method doesn't abort if non-attribute things are found at the end - that should be checked elsewhere
        yield [new Cursor('{: #custom-id } foo'), ['id' => 'custom-id'], ' foo'];
        yield [new Cursor('{: #custom-id }.'), ['id' => 'custom-id'], '.'];

        // Missing curly brace on end
        yield [new Cursor('{: #custom-id'), [], '{: #custom-id'];

        // Two sets of attributes in one string - we stop after the first one
        yield [new Cursor('{: #id1 } {: #id2 }'), ['id' => 'id1'], ' {: #id2 }'];

        // Curly braces inside of values
        yield [new Cursor('{: data-json="{1,2,3}" }'), ['data-json' => '{1,2,3}']];
        yield [new Cursor('{data-json={1,2,3}} test'), ['data-json' => '{1,2,3}'], ' test'];

        // Avoid mustache style templating language being parsed as attributes
        yield [new Cursor('{{ foo }}'), [], '{{ foo }}'];
        yield [new Cursor(' {{ foo }}'), [], ' {{ foo }}'];
        yield [new Cursor('{ foo }}'), [], '{ foo }}'];

        // Issue 1071
        yield [new Cursor('{.display-4.mt-5.mx-auto}'), ['class' => 'display-4 mt-5 mx-auto']];
    }

    /**
     * @dataProvider dataForTestMergeAttributes
     *
     * @param AbstractBlock|AbstractInline|array<string, mixed> $a1
     * @param AbstractBlock|AbstractInline|array<string, mixed> $a2
     * @param array<string, mixed>                              $expected
     */
    public function testMergeAttributes($a1, $a2, array $expected): void
    {
        $this->assertEquals($expected, AttributesHelper::mergeAttributes($a1, $a2));
    }

    /**
     * @return iterable<AbstractBlock|AbstractInline|array<string, mixed>>
     */
    public static function dataForTestMergeAttributes(): iterable
    {
        yield [
            [],
            [],
            [],
        ];

        // The second set of attributes overrides the first one (for matching keys)
        yield [
            ['a' => '1', 'b' => 1],
            ['a' => '2', 'c' => 2],
            ['a' => '2', 'b' => 1, 'c' => 2],
        ];

        // Special handling for the class attribute
        yield [
            ['id' => 'foo', 'class' => 'foo'],
            ['id' => 'bar', 'class' => 'bar'],
            ['id' => 'bar', 'class' => 'foo bar'],
        ];

        $block = new FakeBlock1();

        $block->data->set('attributes', ['id' => 'block', 'class' => 'block']);

        yield [
            $block,
            ['id' => 'foo', 'class' => 'foo'],
            ['id' => 'foo', 'class' => 'block foo'],
        ];

        yield [
            ['id' => 'foo', 'class' => 'foo'],
            $block,
            ['id' => 'block', 'class' => 'foo block'],
        ];

        $inline = new FakeInline1();

        $inline->data->set('attributes', ['id' => 'inline', 'class' => 'inline']);

        yield [
            $inline,
            ['id' => 'foo', 'class' => 'foo'],
            ['id' => 'foo', 'class' => 'inline foo'],
        ];

        yield [
            ['id' => 'foo', 'class' => 'foo'],
            $inline,
            ['id' => 'inline', 'class' => 'foo inline'],
        ];

        yield [
            $block,
            $inline,
            ['id' => 'inline', 'class' => 'block inline'],
        ];

        yield [
            $inline,
            $block,
            ['id' => 'block', 'class' => 'inline block'],
        ];
    }

    /**
     * @dataProvider dataForTestFilterAttributes
     *
     * @param array<string, mixed> $attributes
     * @param list<string>         $allowList
     * @param array<string, mixed> $expected
     */
    public function testFilterAttributes(array $attributes, array $allowList, bool $allowUnsafeLinks, array $expected): void
    {
        $this->assertEquals($expected, AttributesHelper::filterAttributes($attributes, $allowList, $allowUnsafeLinks));
    }

    /**
     * @return iterable<array<mixed>>
     */
    public static function dataForTestFilterAttributes(): iterable
    {
        // No allow list; unsafe links disallowed (default behavior)
        yield [
            ['id' => 'foo', 'class' => 'bar', 'onclick' => 'alert("XSS")', 'href' => 'javascript:alert("XSS")'],
            [],
            false,
            ['id' => 'foo', 'class' => 'bar'],
        ];

        // No allow list; unsafe links allowed
        yield [
            ['id' => 'foo', 'class' => 'bar', 'onclick' => 'alert("XSS")', 'href' => 'javascript:alert("XSS")'],
            [],
            true,
            ['id' => 'foo', 'class' => 'bar', 'href' => 'javascript:alert("XSS")'],
        ];

        // Allow list; unsafe links disallowed
        yield [
            ['id' => 'foo', 'class' => 'bar', 'onclick' => 'alert("XSS")', 'href' => 'javascript:alert("XSS")'],
            ['id', 'onclick', 'href'],
            false,
            ['id' => 'foo', 'onclick' => 'alert("XSS")'],
        ];

        // Allow list; unsafe links allowed
        yield [
            ['id' => 'foo', 'class' => 'bar', 'onclick' => 'alert("XSS")', 'href' => 'javascript:alert("XSS")'],
            ['id', 'onclick', 'href'],
            true,
            ['id' => 'foo', 'onclick' => 'alert("XSS")', 'href' => 'javascript:alert("XSS")'],
        ];

        // Allow list blocks all
        yield [
            ['id' => 'foo', 'class' => '<script>alert("XSS")</script>'],
            ['style'],
            false,
            [],
        ];

        // Can't use weird casing to bypass allowlist or 'on*' restriction
        yield [
            ['ID' => 'foo', 'oNcLiCk' => 'alert("XSS")'],
            ['id', 'class'],
            false,
            [],
        ];
    }
}
