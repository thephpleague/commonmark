<?php

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

class ArrayCollectionTest extends TestCase
{
    public function testConstructorAndToArray()
    {
        $collection = new ArrayCollection();
        $this->assertEquals([], $collection->toArray());

        $array = [];
        $collection = new ArrayCollection($array);
        $this->assertEquals($array, $collection->toArray());

        $array = ['foo' => 'bar'];
        $collection = new ArrayCollection($array);
        $this->assertEquals($array, $collection->toArray());
    }

    public function testFirst()
    {
        $collection = new ArrayCollection(['foo', 'bar']);
        $this->assertEquals('foo', $collection->first());
    }

    public function testLast()
    {
        $collection = new ArrayCollection(['foo', 'bar']);
        $this->assertEquals('bar', $collection->last());
    }

    public function testGetIterator()
    {
        $array = ['foo' => 'bar'];
        $collection = new ArrayCollection($array);
        $iterator = $collection->getIterator();

        $this->assertTrue($iterator instanceof \ArrayIterator);
        $this->assertEquals($array, $iterator->getArrayCopy());
    }

    public function testOffsetExists()
    {
        $collection = new ArrayCollection(['foo' => 1, 'bar', null]);

        $this->assertTrue($collection->offsetExists('foo'));
        $this->assertTrue($collection->offsetExists(0));
        $this->assertTrue($collection->offsetExists(1));
        $this->assertTrue(isset($collection['foo']));
        $this->assertTrue(isset($collection[0]));
        $this->assertTrue(isset($collection[1]));

        $this->assertFalse($collection->offsetExists('FOO'));
        $this->assertFalse($collection->offsetExists(2));
    }

    public function testOffsetGet()
    {
        $collection = new ArrayCollection(['foo' => 1, 'bar']);

        $this->assertEquals(1, $collection['foo']);
        $this->assertEquals(1, $collection->offsetGet('foo'));
        $this->assertEquals('bar', $collection[0]);
        $this->assertEquals('bar', $collection->offsetGet(0));
        $this->assertNull($collection->offsetGet('bar'));
    }

    public function testOffsetSet()
    {
        $collection = new ArrayCollection();

        $collection[] = 'foo';
        $this->assertEquals(['foo'], $collection->toArray());

        $collection[] = 'bar';
        $this->assertEquals(['foo', 'bar'], $collection->toArray());

        $collection = new ArrayCollection(['foo']);

        $collection['foo'] = 1;
        $this->assertEquals(['foo', 'foo' => 1], $collection->toArray());

        $collection['foo'] = 2;
        $this->assertEquals(['foo', 'foo' => 2], $collection->toArray());
    }

    public function testOffsetUnset()
    {
        $collection = new ArrayCollection(['foo' => 1, 'bar', 'baz']);

        unset($collection['foo']);
        $this->assertEquals(['bar', 'baz'], $collection->toArray());

        unset($collection['foo']);
        $this->assertEquals(['bar', 'baz'], $collection->toArray());

        unset($collection[0]);
        $this->assertEquals([1 => 'baz'], $collection->toArray());

        unset($collection[1]);
        $this->assertEquals([], $collection->toArray());
    }

    public function testOffsetUnsetWithNulls()
    {
        $collection = new ArrayCollection(['foo' => null]);

        unset($collection['nonExistantKey']);
        $this->assertEquals(['foo' => null], $collection->toArray());

        unset($collection['foo']);
        $this->assertEquals([], $collection->toArray());
    }

    public function testSlice()
    {
        $collection = new ArrayCollection(['foo' => 1, 0 => 'bar', 1 => 'baz', 2 => 2]);

        $this->assertEquals(['foo' => 1, 0 => 'bar', 1 => 'baz', 2 => 2], $collection->slice(0));
        $this->assertEquals(['foo' => 1, 0 => 'bar', 1 => 'baz', 2 => 2], $collection->slice(0, null));
        $this->assertEquals(['foo' => 1, 0 => 'bar', 1 => 'baz', 2 => 2], $collection->slice(0, 99));
        $this->assertEquals(['foo' => 1, 0 => 'bar', 1 => 'baz', 2 => 2], $collection->slice(0, 4));
        $this->assertEquals(['foo' => 1, 0 => 'bar', 1 => 'baz'], $collection->slice(0, 3));
        $this->assertEquals(['foo' => 1, 0 => 'bar'], $collection->slice(0, 2));
        $this->assertEquals(['foo' => 1], $collection->slice(0, 1));
        $this->assertEquals([], $collection->slice(0, 0));

        $this->assertEquals([0 => 'bar', 1 => 'baz', 2 => 2], $collection->slice(1));
        $this->assertEquals([0 => 'bar', 1 => 'baz', 2 => 2], $collection->slice(1, null));
        $this->assertEquals([0 => 'bar', 1 => 'baz', 2 => 2], $collection->slice(1, 99));
        $this->assertEquals([0 => 'bar', 1 => 'baz', 2 => 2], $collection->slice(1, 3));
        $this->assertEquals([0 => 'bar', 1 => 'baz'], $collection->slice(1, 2));
        $this->assertEquals([0 => 'bar'], $collection->slice(1, 1));
        $this->assertEquals([], $collection->slice(1, 0));

        $this->assertEquals([1 => 'baz', 2 => 2], $collection->slice(2));
        $this->assertEquals([1 => 'baz', 2 => 2], $collection->slice(2, null));
        $this->assertEquals([1 => 'baz', 2 => 2], $collection->slice(2, 99));
        $this->assertEquals([1 => 'baz', 2 => 2], $collection->slice(2, 2));
        $this->assertEquals([1 => 'baz'], $collection->slice(2, 1));
        $this->assertEquals([], $collection->slice(2, 0));

        $this->assertEquals([2 => 2], $collection->slice(3));
        $this->assertEquals([2 => 2], $collection->slice(3, null));
        $this->assertEquals([2 => 2], $collection->slice(3, 99));
        $this->assertEquals([2 => 2], $collection->slice(3, 1));
        $this->assertEquals([], $collection->slice(3, 0));

        $this->assertEquals([], $collection->slice(4));
        $this->assertEquals([], $collection->slice(99));
        $this->assertEquals([], $collection->slice(99, 99));
    }

    public function testToArray()
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
