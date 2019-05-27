<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Delimiter;

use League\CommonMark\Delimiter\Delimiter;
use League\CommonMark\Inline\Element\AbstractStringContainer;
use PHPUnit\Framework\TestCase;

class DelimiterTest extends TestCase
{
    public function testConstructorAndGetters()
    {
        $node = $this->createMock(AbstractStringContainer::class);

        $delimiter = new Delimiter('*', 2, $node, true, false, null);
        $this->assertSame('*', $delimiter->getChar());
        $this->assertSame(2, $delimiter->getLength());
        $this->assertSame(2, $delimiter->getOriginalLength());
        $this->assertSame($node, $delimiter->getInlineNode());
        $this->assertTrue($delimiter->canOpen());
        $this->assertFalse($delimiter->canClose());
        $this->assertNull($delimiter->getIndex());

        $delimiter = new Delimiter('_', 1, $node, false, true, 7);
        $this->assertSame('_', $delimiter->getChar());
        $this->assertSame(1, $delimiter->getLength());
        $this->assertSame(1, $delimiter->getOriginalLength());
        $this->assertSame($node, $delimiter->getInlineNode());
        $this->assertFalse($delimiter->canOpen());
        $this->assertTrue($delimiter->canClose());
        $this->assertSame(7, $delimiter->getIndex());
    }

    public function testSetCanClose()
    {
        $node = $this->createMock(AbstractStringContainer::class);
        $delimiter = new Delimiter('*', 2, $node, true, false, null);

        $delimiter->setCanClose(true);
        $this->assertTrue($delimiter->canClose());
    }

    public function testSetCanOpen()
    {
        $node = $this->createMock(AbstractStringContainer::class);
        $delimiter = new Delimiter('*', 2, $node, true, false, null);

        $delimiter->setCanOpen(false);
        $this->assertFalse($delimiter->canOpen());
    }

    public function testSetActive()
    {
        $node = $this->createMock(AbstractStringContainer::class);
        $delimiter = new Delimiter('*', 2, $node, true, false, null);

        $delimiter->setActive(true);
        $this->assertTrue($delimiter->isActive());

        $delimiter->setActive(false);
        $this->assertFalse($delimiter->isActive());
    }

    public function testSetChar()
    {
        $node = $this->createMock(AbstractStringContainer::class);
        $delimiter = new Delimiter('*', 2, $node, true, false, null);

        $delimiter->setChar('_');
        $this->assertSame('_', $delimiter->getChar());
    }

    public function testSetIndex()
    {
        $node = $this->createMock(AbstractStringContainer::class);
        $delimiter = new Delimiter('*', 2, $node, true, false, null);

        $delimiter->setIndex(7);
        $this->assertSame(7, $delimiter->getIndex());

        $delimiter->setIndex(null);
        $this->assertNull($delimiter->getIndex());
    }

    public function testSetNext()
    {
        $node = $this->createMock(AbstractStringContainer::class);
        $delimiter = new Delimiter('*', 2, $node, true, false, null);

        $delimiter->setNext($delimiter);
        $this->assertSame($delimiter, $delimiter->getNext());

        $delimiter->setNext(null);
        $this->assertNull($delimiter->getNext());
    }

    public function testSetLength()
    {
        $node = $this->createMock(AbstractStringContainer::class);
        $delimiter = new Delimiter('*', 2, $node, true, false, null);

        $delimiter->setLength(3);
        $this->assertSame(3, $delimiter->getLength());
        $this->assertSame(2, $delimiter->getOriginalLength());
    }

    public function testSetInlineNode()
    {
        $node = $this->createMock(AbstractStringContainer::class);
        $delimiter = new Delimiter('*', 2, $node, true, false, null);

        $node2 = $this->createMock(AbstractStringContainer::class);

        $delimiter->setInlineNode($node2);
        $this->assertSame($node2, $delimiter->getInlineNode());
    }

    public function testSetPrevious()
    {
        $node = $this->createMock(AbstractStringContainer::class);
        $delimiter = new Delimiter('*', 2, $node, true, false, null);

        $delimiter->setPrevious($delimiter);
        $this->assertSame($delimiter, $delimiter->getPrevious());

        $delimiter->setPrevious(null);
        $this->assertNull($delimiter->getPrevious());
    }
}
