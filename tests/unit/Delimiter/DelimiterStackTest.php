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

namespace League\CommonMark\Tests\Unit\Delimiter;

use League\CommonMark\Delimiter\Bracket;
use League\CommonMark\Delimiter\Delimiter;
use League\CommonMark\Delimiter\DelimiterStack;
use League\CommonMark\Node\Inline\Text;
use PHPUnit\Framework\TestCase;

final class DelimiterStackTest extends TestCase
{
    public function testPush(): void
    {
        $text1 = new Text('*');
        $text2 = new Text('_');
        $text3 = new Text('*');

        $delim1 = new Delimiter('*', 1, $text1, true, false);
        $delim2 = new Delimiter('_', 1, $text2, false, false);
        $delim3 = new Delimiter('*', 1, $text3, false, true);

        $stack = new DelimiterStack();

        $this->assertNull($stack->searchByCharacter('*'));
        $this->assertNull($stack->searchByCharacter('_'));

        $stack->push($delim1);

        $this->assertSame($delim1, $stack->searchByCharacter('*'));
        $this->assertNull($stack->searchByCharacter('_'));
        $this->assertNull($delim1->getPrevious());
        $this->assertNull($delim1->getNext());
        $this->assertNull($delim2->getPrevious());
        $this->assertNull($delim2->getNext());
        $this->assertNull($delim3->getPrevious());
        $this->assertNull($delim3->getNext());

        $stack->push($delim2);

        $this->assertSame($delim1, $stack->searchByCharacter('*'));
        $this->assertSame($delim2, $stack->searchByCharacter('_'));
        $this->assertNull($delim1->getPrevious());
        $this->assertSame($delim2, $delim1->getNext());
        $this->assertSame($delim1, $delim2->getPrevious());
        $this->assertNull($delim2->getNext());
        $this->assertNull($delim3->getPrevious());
        $this->assertNull($delim3->getNext());

        $stack->push($delim3);

        $this->assertSame($delim3, $stack->searchByCharacter('*'));
        $this->assertSame($delim2, $stack->searchByCharacter('_'));
        $this->assertNull($delim1->getPrevious());
        $this->assertSame($delim2, $delim1->getNext());
        $this->assertSame($delim1, $delim2->getPrevious());
        $this->assertSame($delim3, $delim2->getNext());
        $this->assertSame($delim2, $delim3->getPrevious());
        $this->assertNull($delim3->getNext());
    }

    public function testRemoveDelimiter(): void
    {
        $text1 = new Text('*');
        $text2 = new Text('_');
        $text3 = new Text('*');

        $stack = new DelimiterStack();
        $stack->push($delim1 = new Delimiter('*', 1, $text1, true, false));
        $stack->push($delim2 = new Delimiter('_', 1, $text2, false, false));
        $stack->push($delim3 = new Delimiter('*', 1, $text3, false, true));

        $this->assertNull($delim1->getPrevious());
        $this->assertSame($delim2, $delim1->getNext());

        $this->assertSame($delim1, $delim2->getPrevious());
        $this->assertSame($delim3, $delim2->getNext());

        $this->assertSame($delim2, $delim3->getPrevious());
        $this->assertNull($delim3->getNext());

        $stack->removeDelimiter($delim2);

        $this->assertNull($delim1->getPrevious());
        $this->assertSame($delim3, $delim1->getNext());

        $this->assertNull($delim2->getPrevious());
        $this->assertNull($delim2->getNext());

        $this->assertSame($delim1, $delim3->getPrevious());
        $this->assertNull($delim3->getNext());

        $stack->removeDelimiter($delim3);

        $this->assertNull($delim1->getPrevious());
        $this->assertNull($delim1->getNext());

        $this->assertNull($delim3->getPrevious());
        $this->assertNull($delim3->getNext());
    }

    public function testRemoveDelimitersBetween(): void
    {
        $text1 = new Text('*');
        $text2 = new Text('_');
        $text3 = new Text('*');
        $text4 = new Text('_');
        $text5 = new Text('*');

        $stack = new DelimiterStack();
        $stack->push($delim1 = new Delimiter('*', 1, $text1, false, false));
        $stack->push($delim2 = new Delimiter('_', 1, $text2, false, false));
        $stack->push($delim3 = new Delimiter('*', 1, $text3, false, false));
        $stack->push($delim4 = new Delimiter('_', 1, $text4, false, false));
        $stack->push($delim5 = new Delimiter('*', 1, $text5, false, false));

        // Make removeDelimiterBetween() callable using reflection
        $reflection = new \ReflectionClass($stack);
        $method     = $reflection->getMethod('removeDelimitersBetween');
        $method->setAccessible(true);

        $method->invoke($stack, $delim1, $delim5);

        $this->assertNull($delim1->getPrevious());
        $this->assertSame($delim5, $delim1->getNext());

        $this->assertNull($delim2->getPrevious());
        $this->assertNull($delim2->getNext());

        $this->assertNull($delim3->getPrevious());
        $this->assertNull($delim3->getNext());

        $this->assertNull($delim4->getPrevious());
        $this->assertNull($delim4->getNext());

        $this->assertSame($delim1, $delim5->getPrevious());
        $this->assertNull($delim5->getNext());
    }

    public function testFindEarliest(): void
    {
        $text1 = new Text('*');
        $text2 = new Text('_');
        $text3 = new Text('*');

        $stack = new DelimiterStack();
        $stack->push($delim1 = new Delimiter('*', 1, $text1, true, false));
        $stack->push($delim2 = new Delimiter('_', 1, $text2, false, false));
        $stack->push($delim3 = new Delimiter('*', 1, $text3, false, true));

        // Use reflection to make findEarliest() callable
        $reflection = new \ReflectionClass($stack);
        $method     = $reflection->getMethod('findEarliest');
        $method->setAccessible(true);

        $this->assertSame($delim1, $method->invoke($stack));
        $this->assertSame($delim2, $method->invoke($stack, $delim1));
        $this->assertSame($delim3, $method->invoke($stack, $delim2));
        $this->assertNull($method->invoke($stack, $delim3));
    }

    public function testRemoveAll(): void
    {
        $text1 = new Text('*');
        $text2 = new Text('_');
        $text3 = new Text('*');

        $stack = new DelimiterStack();
        $stack->push($delim1 = new Delimiter('*', 1, $text1, true, false));
        $stack->push($delim2 = new Delimiter('_', 1, $text2, false, false));
        $stack->push($delim3 = new Delimiter('*', 1, $text3, false, true));

        $stack->removeAll();

        $this->assertNull($delim1->getPrevious());
        $this->assertNull($delim1->getNext());

        $this->assertNull($delim2->getPrevious());
        $this->assertNull($delim2->getNext());

        $this->assertNull($delim3->getPrevious());
        $this->assertNull($delim3->getNext());

        $this->assertNull($stack->searchByCharacter('*'));
        $this->assertNull($stack->searchByCharacter('_'));
    }

    public function testRemoveAllWithStackBottomGiven(): void
    {
        $text1 = new Text('*');
        $text2 = new Text('_');
        $text3 = new Text('*');

        $stack = new DelimiterStack();
        $stack->push($delim1 = new Delimiter('*', 1, $text1, true, false));
        $stack->push($delim2 = new Delimiter('_', 1, $text2, false, false));
        $stack->push($delim3 = new Delimiter('*', 1, $text3, false, true));

        $stack->removeAll($delim2);

        $this->assertSame($delim1, $stack->searchByCharacter('*'));
        $this->assertSame($delim2, $stack->searchByCharacter('_'));

        $this->assertNull($delim1->getPrevious());
        $this->assertSame($delim2, $delim1->getNext());

        $this->assertSame($delim1, $delim2->getPrevious());
        $this->assertNull($delim2->getNext());

        $this->assertNull($delim3->getPrevious());
        $this->assertNull($delim3->getNext());
    }

    public function testRemoveEarlierMatches(): void
    {
        $text1 = new Text('*');
        $text2 = new Text('_');
        $text3 = new Text('*');

        $stack = new DelimiterStack();
        $stack->push($delim1 = new Delimiter('*', 1, $text1, true, false));
        $stack->push($delim2 = new Delimiter('_', 1, $text2, false, false));
        $stack->push($delim3 = new Delimiter('*', 1, $text3, false, true));

        $this->assertTrue($delim1->isActive());
        $this->assertTrue($delim2->isActive());
        $this->assertTrue($delim3->isActive());

        $stack->removeEarlierMatches('*');

        $this->assertFalse($delim1->isActive());
        $this->assertTrue($delim2->isActive());
        $this->assertFalse($delim3->isActive());

        $stack->removeEarlierMatches('_');
        $this->assertFalse($delim2->isActive());
    }

    public function testAddBracket(): void
    {
        $text1 = new Text('[');
        $text2 = new Text('*');
        $text3 = new Text(']');

        $stack = new DelimiterStack();

        $stack->addBracket($text1, 0, false);

        $firstBracket = $stack->getLastBracket();
        $this->assertSame($text1, $firstBracket->getNode());
        $this->assertSame(0, $firstBracket->getIndex());
        $this->assertFalse($firstBracket->isImage());
        $this->assertNull($firstBracket->getPrevious());
        $this->assertNull($firstBracket->getPreviousDelimiter());
        $this->assertFalse($firstBracket->hasNext());

        $stack->push(new Delimiter('*', 1, $text2, true, false));
        $stack->addBracket($text3, 2, true);

        $this->assertSame($text3, $stack->getLastBracket()->getNode());
        $this->assertSame(2, $stack->getLastBracket()->getIndex());
        $this->assertTrue($stack->getLastBracket()->isImage());
        $this->assertSame($firstBracket, $stack->getLastBracket()->getPrevious());
        $this->assertSame($text2, $stack->getLastBracket()->getPreviousDelimiter()->getInlineNode());
        $this->assertFalse($stack->getLastBracket()->hasNext());

        $this->assertTrue($firstBracket->hasNext());
    }

    public function testRemoveBracket(): void
    {
        $text1 = new Text('[');
        $text2 = new Text(']');

        $stack = new DelimiterStack();

        $stack->addBracket($text1, 0, false);
        $firstBracket = $stack->getLastBracket();

        $stack->addBracket($text2, 1, false);

        $this->assertSame($text2, $stack->getLastBracket()->getNode());
        $this->assertTrue($firstBracket->hasNext());

        $stack->removeBracket();

        $this->assertSame($text1, $stack->getLastBracket()->getNode());
        $this->assertFalse($firstBracket->hasNext());
    }

    public function testDeactivateLinkOpeners(): void
    {
        $text1 = new Text('[');
        $text2 = new Text('[');
        $text3 = new Text('[');

        $stack = new DelimiterStack();

        $stack->addBracket($text1, 0, false);
        $bracket1 = $stack->getLastBracket();
        $this->assertTrue($bracket1->isActive());

        $stack->addBracket($text2, 1, true);
        $bracket2 = $stack->getLastBracket();
        $this->assertTrue($bracket2->isActive());

        $stack->addBracket($text3, 2, false);
        $bracket3 = $stack->getLastBracket();
        $this->assertTrue($bracket3->isActive());

        $stack->deactivateLinkOpeners();

        $this->assertSame($bracket3, $stack->getLastBracket());

        $this->assertFalse($bracket1->isActive());
        $this->assertFalse($bracket2->isActive());
        $this->assertFalse($bracket3->isActive());
    }
}
