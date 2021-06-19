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

use League\CommonMark\Delimiter\Delimiter;
use League\CommonMark\Node\Inline\AbstractStringContainer;
use PHPUnit\Framework\TestCase;

final class DelimiterTest extends TestCase
{
    public function testConstructorAndGetters(): void
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

    public function testSetActive(): void
    {
        $node      = $this->createMock(AbstractStringContainer::class);
        $delimiter = new Delimiter('*', 2, $node, true, false, null);

        $delimiter->setActive(true);
        $this->assertTrue($delimiter->isActive());

        $delimiter->setActive(false);
        $this->assertFalse($delimiter->isActive());
    }

    public function testSetNext(): void
    {
        $node      = $this->createMock(AbstractStringContainer::class);
        $delimiter = new Delimiter('*', 2, $node, true, false, null);

        $delimiter->setNext($delimiter);
        $this->assertSame($delimiter, $delimiter->getNext());

        $delimiter->setNext(null);
        $this->assertNull($delimiter->getNext());
    }

    public function testSetLength(): void
    {
        $node      = $this->createMock(AbstractStringContainer::class);
        $delimiter = new Delimiter('*', 2, $node, true, false, null);

        $delimiter->setLength(3);
        $this->assertSame(3, $delimiter->getLength());
        $this->assertSame(2, $delimiter->getOriginalLength());
    }

    public function testSetPrevious(): void
    {
        $node      = $this->createMock(AbstractStringContainer::class);
        $delimiter = new Delimiter('*', 2, $node, true, false, null);

        $delimiter->setPrevious($delimiter);
        $this->assertSame($delimiter, $delimiter->getPrevious());

        $delimiter->setPrevious(null);
        $this->assertNull($delimiter->getPrevious());
    }
}
