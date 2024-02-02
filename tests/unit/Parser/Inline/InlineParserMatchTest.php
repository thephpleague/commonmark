<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Parser\Inline;

use League\CommonMark\Exception\InvalidArgumentException;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use PHPUnit\Framework\TestCase;

final class InlineParserMatchTest extends TestCase
{
    /**
     * @dataProvider provideDataForTesting
     */
    public function testGetRegex(InlineParserMatch $definition, string $expectedRegex): void
    {
        $this->assertSame($expectedRegex, $definition->getRegex());
    }

    /**
     * @return iterable<array{0: InlineParserMatch, 1: string}>
     */
    public static function provideDataForTesting(): iterable
    {
        yield [InlineParserMatch::string('.'), '/\./i'];
        yield [InlineParserMatch::string('...'), '/\.\.\./i'];
        yield [InlineParserMatch::string('foo'), '/foo/i'];
        yield [InlineParserMatch::string('foo')->caseSensitive(), '/foo/'];
        yield [InlineParserMatch::string('🎉'), '/🎉/i'];
        yield [InlineParserMatch::string('/r/'), '/\/r\//i'];
        yield [InlineParserMatch::oneOf('foo', 'bar'), '/foo|bar/i'];
        yield [InlineParserMatch::oneOf('foo', 'bar')->caseSensitive(), '/foo|bar/'];
        yield [InlineParserMatch::oneOf('foo', '.', '[x]'), '/foo|\.|\[x\]/i'];
        yield [InlineParserMatch::regex('[\w-_]{3,}'), '/[\w-_]{3,}/i'];
        yield [InlineParserMatch::regex('[\w-_]{3,}')->caseSensitive(), '/[\w-_]{3,}/'];

        $complexExample = InlineParserMatch::join(
            InlineParserMatch::string('foo'),
            InlineParserMatch::oneOf('bar', 'baz'),
            InlineParserMatch::regex('\d+')
        );

        yield [$complexExample, '/(foo)(bar|baz)(\d+)/i'];

        $complexExampleCaseSensitive = InlineParserMatch::join(
            InlineParserMatch::string('foo'),
            InlineParserMatch::oneOf('bar', 'baz'),
            InlineParserMatch::regex('\d+')
        )->caseSensitive();

        yield [$complexExampleCaseSensitive->caseSensitive(), '/(foo)(bar|baz)(\d+)/'];

        $complexExampleCaseSensitiveIndividually = InlineParserMatch::join(
            InlineParserMatch::string('foo')->caseSensitive(),
            InlineParserMatch::oneOf('bar', 'baz')->caseSensitive(),
            InlineParserMatch::regex('\d+')->caseSensitive()
        );

        yield [$complexExampleCaseSensitiveIndividually, '/(foo)(bar|baz)(\d+)/'];
    }

    public function testCannotMixCaseSensitivity(): void
    {
        $this->expectException(InvalidArgumentException::class);

        InlineParserMatch::join(
            InlineParserMatch::string('foo')->caseSensitive(),
            InlineParserMatch::oneOf('bar', 'baz'),
            InlineParserMatch::regex('\d+')
        );
    }
}
