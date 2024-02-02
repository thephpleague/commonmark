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

namespace League\CommonMark\Tests\Unit\Parser;

use League\CommonMark\Exception\UnexpectedEncodingException;
use League\CommonMark\Parser\Cursor;
use PHPUnit\Framework\TestCase;

final class CursorTest extends TestCase
{
    public function testConstructor(): void
    {
        $cursor = new Cursor('foo');
        $this->assertEquals('foo', $cursor->getLine());
    }

    public function testConstructorWithInvalidUTF8(): void
    {
        $this->expectException(UnexpectedEncodingException::class);

        new Cursor(\hex2bin('A5A5A5'));
    }

    /**
     * @dataProvider dataForTestingNextNonSpaceMethods
     */
    public function testGetNextNonSpacePosition(string $string, int $expectedPosition, ?string $expectedCharacter): void
    {
        $cursor = new Cursor($string);

        $this->assertEquals($expectedPosition, $cursor->getNextNonSpacePosition());
        $this->assertEquals($expectedPosition, $cursor->getNextNonSpacePosition());
    }

    /**
     * @dataProvider dataForTestingNextNonSpaceMethods
     */
    public function testGetNextNonSpaceCharacter(string $string, int $expectedPosition, ?string $expectedCharacter): void
    {
        $cursor = new Cursor($string);

        $this->assertEquals($expectedCharacter, $cursor->getNextNonSpaceCharacter());
        $this->assertEquals($expectedCharacter, $cursor->getNextNonSpaceCharacter());
    }

    /**
     * @return iterable<array<mixed>>
     */
    public static function dataForTestingNextNonSpaceMethods(): iterable
    {
        return [
            ['', 0, null],
            [' ', 1, null],
            ['  ', 2, null],
            ['foo', 0, 'f'],
            [' foo', 1, 'f'],
            ['  foo', 2, 'f'],
            ['тест', 0, 'т'],
            [' т', 1, 'т'],
        ];
    }

    /**
     * @dataProvider dataForGetIndentTest
     */
    public function testGetIndent(string $string, int $position, int $expectedValue): void
    {
        $cursor = new Cursor($string);
        $cursor->advanceBy($position);

        $this->assertEquals($expectedValue, $cursor->getIndent());
    }

    /**
     * @return iterable<array<mixed>>
     */
    public static function dataForGetIndentTest(): iterable
    {
        return [
            ['', 0, 0],
            [' ', 0, 1],
            [' ', 1, 0],
            ['    ', 0, 4],
            ['    ', 1, 3],
            ['    ', 2, 2],
            ['    ', 3, 1],
            ['foo', 0, 0],
            ['foo', 1, 0],
            [' foo', 0, 1],
            [' foo', 1, 0],
            ['  foo', 0, 2],
            ['  foo', 1, 1],
            ['  foo', 2, 0],
            ['  foo', 3, 0],
            ['тест', 0, 0],
            ['тест', 1, 0],
            [' тест', 0, 1],
            [' тест', 1, 0],
        ];
    }

    /**
     * @dataProvider dataForGetCharacterTest
     */
    public function testGetCharacter(string $string, ?int $index, string $expectedValue): void
    {
        $cursor = new Cursor($string);

        $this->assertEquals($expectedValue, $cursor->getCharacter($index));
    }

    /**
     * @dataProvider dataForGetCharacterTest
     */
    public function testGetCurrentCharacter(string $string, ?int $index, string $expectedValue): void
    {
        $cursor = new Cursor($string);
        if ($index !== null) {
            $cursor->advanceBy($index);
        }

        $this->assertEquals($expectedValue, $cursor->getCurrentCharacter());
    }

    /**
     * @return iterable<array<mixed>>
     */
    public static function dataForGetCharacterTest(): iterable
    {
        return [
            ['', null, ''],
            ['', 0, ''],
            ['', 1, ''],
            ['foo', null, 'f'],
            ['foo', 0, 'f'],
            ['foo', 1, 'o'],
            [' тест ', 0, ' '],
            [' тест ', 1, 'т'],
            [' тест ', 2, 'е'],
            [' тест ', 3, 'с'],
            [' тест ', 4, 'т'],
            [' тест ', 5, ' '],
        ];
    }

    /**
     * @dataProvider dataForPeekTest
     */
    public function testPeek(string $string, int $position, string $expectedValue): void
    {
        $cursor = new Cursor($string);
        $cursor->advanceBy($position);

        $this->assertEquals($expectedValue, $cursor->peek());
    }

    /**
     * @return iterable<array<mixed>>
     */
    public static function dataForPeekTest(): iterable
    {
        return [
            ['', 0, ''],
            [' ', 0, ''],
            ['', 99, ''],
            ['foo', 0, 'o'],
            ['bar', 1, 'r'],
            ['тест ', 1, 'с'],
        ];
    }

    /**
     * @dataProvider dataForIsLineBlankTest
     */
    public function testIsLineBlank(string $string, bool $expectedValue): void
    {
        $cursor = new Cursor($string);

        $this->assertEquals($expectedValue, $cursor->isBlank());
    }

    /**
     * @return iterable<array<mixed>>
     */
    public static function dataForIsLineBlankTest(): iterable
    {
        return [
            ['', true],
            [' ', true],
            ['      ', true],
            ['foo', false],
            ['   foo', false],
            ['тест', false],
        ];
    }

    /**
     * @dataProvider dataForAdvanceTest
     */
    public function testAdvance(string $string, int $numberOfAdvances, int $expectedPosition): void
    {
        $cursor = new Cursor($string);
        while ($numberOfAdvances--) {
            $cursor->advance();
        }

        $this->assertEquals($expectedPosition, $cursor->getPosition());
    }

    /**
     * @return iterable<array<mixed>>
     */
    public static function dataForAdvanceTest(): iterable
    {
        return [
            ['', 0, 0],
            ['', 1, 0],
            ['', 99, 0],
            ['foo', 0, 0],
            ['foo', 1, 1],
            ['foo', 2, 2],
            ['foo', 3, 3],
            ['foo', 9, 3],
            ['тест', 0, 0],
            ['тест', 1, 1],
            ['тест', 2, 2],
            ['тест', 3, 3],
            ['тест', 4, 4],
            ['тест', 9, 4],
        ];
    }

    /**
     * @dataProvider dataForAdvanceTestBy
     */
    public function testAdvanceBy(string $string, int $advance, int $expectedPosition): void
    {
        $cursor = new Cursor($string);
        $cursor->advanceBy($advance);

        $this->assertEquals($expectedPosition, $cursor->getPosition());
    }

    /**
     * @return iterable<array<mixed>>
     */
    public static function dataForAdvanceTestBy(): iterable
    {
        return [
            ['', 0, 0],
            ['', 1, 0],
            ['', 99, 0],
            ['foo', 0, 0],
            ['foo', 1, 1],
            ['foo', 2, 2],
            ['foo', 3, 3],
            ['foo', 9, 3],
            ['тест', 0, 0],
            ['тест', 1, 1],
            ['тест', 2, 2],
            ['тест', 3, 3],
            ['тест', 4, 4],
            ['тест', 9, 4],
            ["aa\t1234", 7, 7],
        ];
    }

    public function testAdvanceByZero(): void
    {
        $cursor = new Cursor('foo bar');
        $cursor->advance();
        $this->assertEquals(1, $cursor->getPosition());
        $cursor->advanceBy(0);
        $this->assertEquals(1, $cursor->getPosition());
    }

    public function testAdvanceByColumnOffset(): void
    {
        $cursor = new Cursor("1. \t\tthere");
        $cursor->advanceBy(3);

        $this->assertEquals(5, $cursor->getIndent());
        $this->assertEquals(3, $cursor->getPosition());
        $this->assertEquals(3, $cursor->getColumn());

        $cursor->advanceBy(4, true);

        $this->assertEquals(1, $cursor->getIndent());
        $this->assertEquals(4, $cursor->getPosition());
        $this->assertEquals(7, $cursor->getColumn());
    }

    /**
     * @dataProvider dataForAdvanceToNextNonSpaceTest
     */
    public function testAdvanceToNextNonSpace(string $subject, int $startPos, int $expectedResult): void
    {
        $cursor = new Cursor($subject);
        $cursor->advanceBy($startPos);

        $this->assertEquals($expectedResult, $cursor->advanceToNextNonSpaceOrTab());

        $this->assertSame($cursor->getPosition(), $cursor->getNextNonSpacePosition());
        $this->assertSame(0, $cursor->getIndent());
    }

    /**
     * @return iterable<array<mixed>>
     */
    public static function dataForAdvanceToNextNonSpaceTest(): iterable
    {
        return [
            ['', 0, 0],
            [' ', 0, 1],
            [' ', 1, 0],
            ['  ', 0, 2],
            ['  ', 1, 1],
            ['  ', 2, 0],
            ['foo bar', 0, 0],
            ['foo bar', 3, 1],
            ['foo bar', 4, 0],
            ['это тест', 0, 0],
            ['это тест', 3, 1],
            ['это тест', 4, 0],
            ["\tbar", 0, 1],
            ["  \n  \n  ", 0, 2],
            ["  \n  \n  ", 1, 1],
            ["  \n  \n  ", 2, 0],
            ["  \n  \n  ", 3, 2],
            ["  \n  \n  ", 4, 1],
        ];
    }

    /**
     * @dataProvider dataForAdvanceToNextNonSpaceOrNewlineTest
     */
    public function testAdvanceToNextNonSpaceOrNewline(string $subject, int $startPos, int $expectedResult): void
    {
        $cursor = new Cursor($subject);
        $cursor->advanceBy($startPos);

        $this->assertEquals($expectedResult, $cursor->advanceToNextNonSpaceOrNewline());
    }

    /**
     * @return iterable<array<mixed>>
     */
    public static function dataForAdvanceToNextNonSpaceOrNewlineTest(): iterable
    {
        return [
            ['', 0, 0],
            [' ', 0, 1],
            [' ', 1, 0],
            ['  ', 0, 2],
            ['  ', 1, 1],
            ['  ', 2, 0],
            ['foo bar', 0, 0],
            ['foo bar', 3, 1],
            ['foo bar', 4, 0],
            ['это тест', 0, 0],
            ['это тест', 3, 1],
            ['это тест', 4, 0],
            ["\tbar", 0, 0],
            ["  \n  \n  ", 0, 5],
            ["  \n  \n  ", 1, 4],
            ["  \n  \n  ", 2, 3],
            ["  \n  \n  ", 3, 5],
            ["  \n  \n  ", 4, 4],
        ];
    }

    /**
     * @dataProvider dataForGetRemainderTest
     */
    public function testGetRemainder(string $string, int $position, string $expectedResult): void
    {
        $cursor = new Cursor($string);
        $cursor->advanceBy($position);

        $this->assertEquals($expectedResult, $cursor->getRemainder());
    }

    /**
     * @return iterable<array<mixed>>
     */
    public static function dataForGetRemainderTest(): iterable
    {
        return [
            [' ', 0, ' '],
            ['  ', 0, '  '],
            ['  ', 1, ' '],
            ['foo bar', 0, 'foo bar'],
            ['foo bar', 2, 'o bar'],
            ['это тест', 1, 'то тест'],
        ];
    }

    /**
     * @dataProvider dataForIsAtEndTest
     *
     * @param int|false|null $advanceBy
     */
    public function testIsAtEnd(string $string, $advanceBy, bool $expectedValue): void
    {
        $cursor = new Cursor($string);
        if ($advanceBy === null) {
            $cursor->advance();
        } elseif ($advanceBy !== false) {
            $cursor->advanceBy($advanceBy);
        }

        $this->assertEquals($expectedValue, $cursor->isAtEnd());
    }

    /**
     * @return iterable<array<mixed>>
     */
    public static function dataForIsAtEndTest(): iterable
    {
        return [
            ['', false, true],
            [' ', 0, false],
            [' ', null, true],
            [' ', 1, true],
            ['foo', 2, false],
            ['foo', 3, true],
            ['тест', 4, true],
        ];
    }

    /**
     * @dataProvider dataForTestMatch
     */
    public function testMatch(string $string, string $regex, int $initialPosition, int $expectedPosition, string $expectedResult): void
    {
        $cursor = new Cursor($string);
        $cursor->advanceBy($initialPosition);

        $result = $cursor->match($regex);

        $this->assertEquals($expectedResult, $result);
        $this->assertEquals($expectedPosition, $cursor->getPosition());
    }

    /**
     * @return iterable<array<mixed>>
     */
    public static function dataForTestMatch(): iterable
    {
        return [
            ['this is a test', '/[aeiou]s/', 0, 4, 'is'],
            ['this is a test', '/[aeiou]s/', 2, 4, 'is'],
            ['this is a test', '/[aeiou]s/', 3, 7, 'is'],
            ['this is a test', '/[aeiou]s/', 9, 13, 'es'],
            ['Это тест', '/т/u', 0, 2, 'т'],
            ['Это тест', '/т/u', 1, 2, 'т'],
            ['Это тест', '/т/u', 2, 5, 'т'],
        ];
    }

    /**
     * @dataProvider dataForTestGetSubstring
     */
    public function testGetSubstring(string $string, int $start, ?int $length, string $expectedResult): void
    {
        $cursor = new Cursor($string);

        $this->assertSame($expectedResult, $cursor->getSubstring($start, $length));
    }

    /**
     * @return iterable<array<mixed>>
     */
    public static function dataForTestGetSubstring(): iterable
    {
        yield ['Hello', 0, 2, 'He'];
        yield ['Hello', 1, 3, 'ell'];
        yield ['Hello', 1, null, 'ello'];
        yield ['Это тест', 1, -1, 'то тес'];
    }
}
