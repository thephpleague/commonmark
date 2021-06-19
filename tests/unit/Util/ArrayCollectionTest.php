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

namespace League\CommonMark\Tests\Unit\Util;

use League\CommonMark\Util\ArrayCollection;
use PHPUnit\Framework\TestCase;

final class ArrayCollectionTest extends TestCase
{
    public function testConstructorAndToArray(): void
    {
        $collection = new ArrayCollection();
        $this->assertEquals([], $collection->toArray());

        $array      = [];
        $collection = new ArrayCollection($array);
        $this->assertEquals($array, $collection->toArray());

        $array      = ['foo', 'bar'];
        $collection = new ArrayCollection($array);
        $this->assertEquals($array, $collection->toArray());
    }

    public function testFirst(): void
    {
        $collection = new ArrayCollection(['foo', 'bar']);
        $this->assertEquals('foo', $collection->first());
    }

    public function testLast(): void
    {
        $collection = new ArrayCollection(['foo', 'bar']);
        $this->assertEquals('bar', $collection->last());
    }

    public function testGetIterator(): void
    {
        $array      = ['foo', 'bar'];
        $collection = new ArrayCollection($array);
        $iterator   = $collection->getIterator();

        $this->assertTrue($iterator instanceof \ArrayIterator);
        $this->assertEquals($array, $iterator->getArrayCopy());
    }

    public function testOffsetExists(): void
    {
        $collection = new ArrayCollection(['foo', 2 => 'bar', 3, null]);

        $this->assertTrue($collection->offsetExists(0));
        $this->assertTrue($collection->offsetExists(2));
        $this->assertTrue($collection->offsetExists(3));
        $this->assertTrue($collection->offsetExists(4));
        $this->assertTrue(isset($collection[0]));
        $this->assertTrue(isset($collection[2]));
        $this->assertTrue(isset($collection[3]));
        $this->assertTrue(isset($collection[4]));

        $this->assertFalse($collection->offsetExists(1));
        $this->assertFalse(isset($collection[1]));
    }

    public function testOffsetGet(): void
    {
        $collection = new ArrayCollection(['foo', 2 => 'bar']);

        $this->assertEquals('foo', $collection[0]);
        $this->assertEquals('foo', $collection->offsetGet(0));
        $this->assertEquals('bar', $collection[2]);
        $this->assertEquals('bar', $collection->offsetGet(2));
    }

    public function testOffsetSet(): void
    {
        $collection = new ArrayCollection();

        $collection[] = 'foo';
        $this->assertEquals(['foo'], $collection->toArray());

        $collection[] = 'bar';
        $this->assertEquals(['foo', 'bar'], $collection->toArray());

        $collection = new ArrayCollection(['foo']);

        $collection[42] = true;
        $this->assertEquals(['foo', 42 => true], $collection->toArray());

        $collection[42] = false;
        $this->assertEquals(['foo', 42 => false], $collection->toArray());
    }

    public function testOffsetUnset(): void
    {
        $collection = new ArrayCollection(['foo', 'bar', 'baz']);

        unset($collection[0]);
        $this->assertEquals([1 => 'bar', 2 => 'baz'], $collection->toArray());

        unset($collection[9999]);
        $this->assertEquals([1 => 'bar', 2 => 'baz'], $collection->toArray());

        unset($collection[1]);
        $this->assertEquals([2 => 'baz'], $collection->toArray());

        unset($collection[2]);
        $this->assertEquals([], $collection->toArray());
    }

    public function testOffsetUnsetWithNulls(): void
    {
        $collection = new ArrayCollection([2 => null]);

        unset($collection[99999]);
        $this->assertEquals([2 => null], $collection->toArray());

        unset($collection[2]);
        $this->assertEquals([], $collection->toArray());
    }

    public function testSlice(): void
    {
        $collection = new ArrayCollection(['foo', 'bar', 'baz', 42 => 'ftw']);

        $this->assertEquals(['foo', 'bar', 'baz', 42 => 'ftw'], $collection->slice(0));
        $this->assertEquals(['foo', 'bar', 'baz', 42 => 'ftw'], $collection->slice(0, null));
        $this->assertEquals(['foo', 'bar', 'baz', 42 => 'ftw'], $collection->slice(0, 99));
        $this->assertEquals(['foo', 'bar', 'baz', 42 => 'ftw'], $collection->slice(0, 4));
        $this->assertEquals(['foo', 'bar', 'baz'], $collection->slice(0, 3));
        $this->assertEquals(['foo', 'bar'], $collection->slice(0, 2));
        $this->assertEquals(['foo'], $collection->slice(0, 1));
        $this->assertEquals([], $collection->slice(0, 0));

        $this->assertEquals([1 => 'bar', 2 => 'baz', 42 => 'ftw'], $collection->slice(1));
        $this->assertEquals([1 => 'bar', 2 => 'baz', 42 => 'ftw'], $collection->slice(1, null));
        $this->assertEquals([1 => 'bar', 2 => 'baz', 42 => 'ftw'], $collection->slice(1, 99));
        $this->assertEquals([1 => 'bar', 2 => 'baz', 42 => 'ftw'], $collection->slice(1, 3));
        $this->assertEquals([1 => 'bar', 2 => 'baz'], $collection->slice(1, 2));
        $this->assertEquals([1 => 'bar'], $collection->slice(1, 1));
        $this->assertEquals([], $collection->slice(1, 0));

        $this->assertEquals([2 => 'baz', 42 => 'ftw'], $collection->slice(2));
        $this->assertEquals([2 => 'baz', 42 => 'ftw'], $collection->slice(2, null));
        $this->assertEquals([2 => 'baz', 42 => 'ftw'], $collection->slice(2, 99));
        $this->assertEquals([2 => 'baz', 42 => 'ftw'], $collection->slice(2, 2));
        $this->assertEquals([2 => 'baz'], $collection->slice(2, 1));
        $this->assertEquals([], $collection->slice(2, 0));

        $this->assertEquals([42 => 'ftw'], $collection->slice(3));
        $this->assertEquals([42 => 'ftw'], $collection->slice(3, null));
        $this->assertEquals([42 => 'ftw'], $collection->slice(3, 99));
        $this->assertEquals([42 => 'ftw'], $collection->slice(3, 1));
        $this->assertEquals([], $collection->slice(3, 0));

        $this->assertEquals([], $collection->slice(4));
        $this->assertEquals([], $collection->slice(99));
        $this->assertEquals([], $collection->slice(99, 99));
    }

    public function testToArray(): void
    {
        $collection = new ArrayCollection();
        $this->assertEquals([], $collection->toArray());

        $collection = new ArrayCollection([]);
        $this->assertEquals([], $collection->toArray());

        $collection = new ArrayCollection([1]);
        $this->assertEquals([1], $collection->toArray());

        $collection = new ArrayCollection([2 => 1, 'foo']);
        $this->assertEquals([2 => 1, 'foo'], $collection->toArray());
    }
}
