<?php

/*
 * This file is part of the commonmark-php package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit;

use League\CommonMark\Cursor;

class CursorTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $cursor = new Cursor('foo');
        $this->assertEquals('foo', $cursor->getLine());
    }

    /**
     * @param $string
     * @param $expectedPosition
     * @param $expectedCharacter
     *
     * @dataProvider dataForTestingFirstNonSpaceMethods
     */
    public function testGetFirstNonSpacePosition($string, $expectedPosition, $expectedCharacter)
    {
        $cursor = new Cursor($string);

        $this->assertEquals($expectedPosition, $cursor->getFirstNonSpacePosition());
    }

    /**
     * @param $string
     * @param $expectedPosition
     * @param $expectedCharacter
     *
     * @dataProvider dataForTestingFirstNonSpaceMethods
     */
    public function testGetFirstNonSpaceCharacter($string, $expectedPosition, $expectedCharacter)
    {
        $cursor = new Cursor($string);

        $this->assertEquals($expectedCharacter, $cursor->getFirstNonSpaceCharacter());
    }

    public function dataForTestingFirstNonSpaceMethods()
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

    /**
     * @param $subject
     * @param $startPos
     * @param $char
     * @param $maxChars
     * @param $expectedResult
     *
     * @dataProvider dataForAdvanceWhileMatchesTest
     */
    public function testAdvanceWhileMatches($subject, $startPos, $char, $maxChars, $expectedResult)
    {
        $cursor = new Cursor($subject);
        $cursor->advanceBy($startPos);

        $this->assertEquals($expectedResult, $cursor->advanceWhileMatches($char, $maxChars));
    }

    public function dataForAdvanceWhileMatchesTest()
    {
        return [
            [' ', 0, ' ', null, 1],
            ['foo', 0, 'o', null, 0],
            ['foo', 1, 'o', null, 2],
            ['foo', 1, 'o', 0, 0],
            ['foo', 1, 'o', 1, 1],
            ['foo', 1, 'o', 2, 2],
            ['foo', 1, 'o', 3, 2],
            ['foo', 1, 'o', 99, 2],
            ['Россия', 0, 'Р', null, 1],
            ['Россия', 1, 'Р', null, 0],
            ['Россия', 2, 'с', null, 2],
            ['Россия', 2, 'с', 0, 0],
            ['Россия', 2, 'с', 1, 1],
            ['Россия', 2, 'с', 2, 2],
            ['Россия', 2, 'с', 3, 2],
        ];
    }

    /**
     * @param $subject
     * @param $startPos
     * @param $expectedResult
     *
     * @dataProvider dataForAdvanceToFirstNonSpaceTest
     */
    public function testAdvanceToFirstNonSpace($subject, $startPos, $expectedResult)
    {
        $cursor = new Cursor($subject);
        $cursor->advanceBy($startPos);

        $this->assertEquals($expectedResult, $cursor->advanceToFirstNonSpace());
    }

    public function dataForAdvanceToFirstNonSpaceTest()
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
            ['', null, ''],
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
}
