<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit;

use League\CommonMark\Cursor;
use League\CommonMark\Exception\UnexpectedEncodingException;
use PHPUnit\Framework\TestCase;

class CursorTest extends TestCase
{
    public function testConstructor()
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
     * @param $string
     * @param $expectedPosition
     * @param $expectedCharacter
     *
     * @dataProvider dataForTestingNextNonSpaceMethods
     */
    public function testGetNextNonSpacePosition($string, $expectedPosition, $expectedCharacter)
    {
        $cursor = new Cursor($string);

        $this->assertEquals($expectedPosition, $cursor->getNextNonSpacePosition());
    }

    /**
     * @param $string
     * @param $expectedPosition
     * @param $expectedCharacter
     *
     * @dataProvider dataForTestingNextNonSpaceMethods
     */
    public function testGetNextNonSpaceCharacter($string, $expectedPosition, $expectedCharacter)
    {
        $cursor = new Cursor($string);

        $this->assertEquals($expectedCharacter, $cursor->getNextNonSpaceCharacter());
    }

    public function dataForTestingNextNonSpaceMethods()
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
     * @param $string
     * @param $position
     * @param $expectedValue
     *
     * @dataProvider dataForGetIndentTest
     */
    public function testGetIndent($string, $position, $expectedValue)
    {
        $cursor = new Cursor($string);
        $cursor->advanceBy($position);

        $this->assertEquals($expectedValue, $cursor->getIndent());
    }

    public function dataForGetIndentTest()
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
     * @param $string
     * @param $index
     * @param $expectedValue
     *
     * @dataProvider dataForGetCharacterTest
     */
    public function testGetCharacter($string, $index, $expectedValue)
    {
        $cursor = new Cursor($string);

        $this->assertEquals($expectedValue, $cursor->getCharacter($index));
    }

    public function dataForGetCharacterTest()
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
     * @param $string
     * @param $position
     * @param $expectedValue
     *
     * @dataProvider dataForPeekTest
     */
    public function testPeek($string, $position, $expectedValue)
    {
        $cursor = new Cursor($string);
        $cursor->advanceBy($position);

        $this->assertEquals($expectedValue, $cursor->peek());
    }

    public function dataForPeekTest()
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
     * @param $string
     * @param $expectedValue
     *
     * @dataProvider dataForIsLineBlankTest
     */
    public function testIsLineBlank($string, $expectedValue)
    {
        $cursor = new Cursor($string);

        $this->assertEquals($expectedValue, $cursor->isBlank());
    }

    public function dataForIsLineBlankTest()
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
     * @param $string
     * @param $numberOfAdvances
     * @param $expectedPosition
     *
     * @dataProvider dataForAdvanceTest
     */
    public function testAdvance($string, $numberOfAdvances, $expectedPosition)
    {
        $cursor = new Cursor($string);
        while ($numberOfAdvances--) {
            $cursor->advance();
        }

        $this->assertEquals($expectedPosition, $cursor->getPosition());
    }

    public function dataForAdvanceTest()
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
     * @param $string
     * @param $advance
     * @param $expectedPosition
     *
     * @dataProvider dataForAdvanceTestBy
     */
    public function testAdvanceBy($string, $advance, $expectedPosition)
    {
        $cursor = new Cursor($string);
        $cursor->advanceBy($advance);

        $this->assertEquals($expectedPosition, $cursor->getPosition());
    }

    public function dataForAdvanceTestBy()
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

    public function testAdvanceByZero()
    {
        $cursor = new Cursor('foo bar');
        $cursor->advance();
        $this->assertEquals(1, $cursor->getPosition());
        $cursor->advanceBy(0);
        $this->assertEquals(1, $cursor->getPosition());
    }

    public function testAdvanceByColumnOffset()
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
     * @param $subject
     * @param $startPos
     * @param $expectedResult
     *
     * @dataProvider dataForAdvanceToNextNonSpaceTest
     */
    public function testAdvanceToNextNonSpace($subject, $startPos, $expectedResult)
    {
        $cursor = new Cursor($subject);
        $cursor->advanceBy($startPos);

        $this->assertEquals($expectedResult, $cursor->advanceToNextNonSpaceOrTab());
    }

    public function dataForAdvanceToNextNonSpaceTest()
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
     * @param $subject
     * @param $startPos
     * @param $expectedResult
     *
     * @dataProvider dataForAdvanceToNextNonSpaceOrNewlineTest
     */
    public function testAdvanceToNextNonSpaceOrNewline($subject, $startPos, $expectedResult)
    {
        $cursor = new Cursor($subject);
        $cursor->advanceBy($startPos);

        $this->assertEquals($expectedResult, $cursor->advanceToNextNonSpaceOrNewline());
    }

    public function dataForAdvanceToNextNonSpaceOrNewlineTest()
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
     * @param $string
     * @param $position
     * @param $expectedResult
     *
     * @dataProvider dataForGetRemainderTest
     */
    public function testGetRemainder($string, $position, $expectedResult)
    {
        $cursor = new Cursor($string);
        $cursor->advanceBy($position);

        $this->assertEquals($expectedResult, $cursor->getRemainder());
    }

    public function dataForGetRemainderTest()
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
     * @param $string
     * @param $advanceBy
     * @param $expectedValue
     *
     * @dataProvider dataForIsAtEndTest
     */
    public function testIsAtEnd($string, $advanceBy, $expectedValue)
    {
        $cursor = new Cursor($string);
        if ($advanceBy === null) {
            $cursor->advance();
        } elseif ($advanceBy !== false) {
            $cursor->advanceBy($advanceBy);
        }

        $this->assertEquals($expectedValue, $cursor->isAtEnd());
    }

    public function dataForIsAtEndTest()
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
     * @param string $string
     * @param string $regex
     * @param int    $initialPosition
     * @param int    $expectedPosition
     * @param string $expectedResult
     *
     * @dataProvider dataForTestMatch
     */
    public function testMatch($string, $regex, $initialPosition, $expectedPosition, $expectedResult)
    {
        $cursor = new Cursor($string);
        $cursor->advanceBy($initialPosition);

        $result = $cursor->match($regex);

        $this->assertEquals($expectedResult, $result);
        $this->assertEquals($expectedPosition, $cursor->getPosition());
    }

    /**
     * @return array
     */
    public function dataForTestMatch()
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
     * @param string   $string
     * @param int      $start
     * @param int|null $length
     * @param string   $expectedResult
     *
     * @dataProvider dataForTestGetSubstring
     */
    public function testGetSubstring($string, $start, $length, $expectedResult)
    {
        $cursor = new Cursor($string);

        $this->assertSame($expectedResult, $cursor->getSubstring($start, $length));
    }

    public function dataForTestGetSubstring()
    {
        yield ['Hello', 0, 2, 'He'];
        yield ['Hello', 1, 3, 'ell'];
        yield ['Hello', 1, null, 'ello'];
        yield ['Это тест', 1, -1, 'то тес'];
    }
}
