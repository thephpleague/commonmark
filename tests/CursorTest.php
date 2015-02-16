<?php

/*
 * This file is part of the commonmark-php package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests;

use League\CommonMark\Cursor;
use Symfony\Component\Yaml\Parser;

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
        return array(
            array('', 0, null),
            array(' ', 1, null),
            array('  ', 2, null),
            array('foo', 0, 'f'),
            array(' foo', 1, 'f'),
            array('  foo', 2, 'f'),
        );
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
        return array(
            array('', 0, 0),
            array(' ', 0, 1),
            array(' ', 1, 0),
            array('    ', 0, 4),
            array('    ', 1, 3),
            array('    ', 2, 2),
            array('    ', 3, 1),
            array('foo', 0, 0),
            array('foo', 1, 0),
            array(' foo', 0, 1),
            array(' foo', 1, 0),
            array('  foo', 0, 2),
            array('  foo', 1, 1),
            array('  foo', 2, 0),
            array('  foo', 3, 0),
        );
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
        return array(
            array('', null, ''),
            array('', 0, ''),
            array('', 1, ''),
            array('foo', null, 'f'),
            array('foo', 0, 'f'),
            array('foo', 1, 'o'),
        );
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
        return array(
            array('', 0, ''),
            array(' ', 0, ''),
            array('', 99, ''),
            array('foo', 0, 'o'),
            array('bar', 1, 'r'),
        );
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
        return array(
            array('', true),
            array(' ', true),
            array('      ', true),
            array('foo', false),
            array('   foo', false),
        );
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
        return array(
            array('', 0, 0),
            array('', 1, 0),
            array('', 99, 0),
            array('foo', 0, 0),
            array('foo', 1, 1),
            array('foo', 2, 2),
            array('foo', 3, 3),
            array('foo', 9, 3),
        );
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
        return array(
            array('', 0, 0),
            array('', 1, 0),
            array('', 99, 0),
            array('foo', 0, 0),
            array('foo', 1, 1),
            array('foo', 2, 2),
            array('foo', 3, 3),
            array('foo', 9, 3),
        );
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
     * @param $expectedPos
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
        return array(
            array(' ', 0, ' ', null, 1),
            array('foo', 0, 'o', null, 0),
            array('foo', 1, 'o', null, 2),
            array('foo', 1, 'o', 0, 0),
            array('foo', 1, 'o', 1, 1),
            array('foo', 1, 'o', 2, 2),
            array('foo', 1, 'o', 3, 2),
            array('foo', 1, 'o', 99, 2),
        );
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
        return array(
            array('', 0, 0),
            array(' ', 0, 1),
            array(' ', 1, 0),
            array('  ', 0, 2),
            array('  ', 1, 1),
            array('  ', 2, 0),
            array('foo bar', 0, 0),
            array('foo bar', 3, 1),
            array("  \n  \n  ", 0, 5),
            array("  \n  \n  ", 1, 4),
            array("  \n  \n  ", 2, 3),
            array("  \n  \n  ", 3, 5),
            array("  \n  \n  ", 4, 4),
        );
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
        return array(
            array('', null, ''),
            array(' ', 0, ' '),
            array('  ', 0, '  '),
            array('  ', 1, ' '),
        );
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
        return array(
            array('', false, true),
            array(' ', 0, false),
            array(' ', null, true),
            array(' ', 1, true)
        );
    }
}
