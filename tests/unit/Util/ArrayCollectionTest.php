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

    /**
     * @group legacy
     */
    public function testAdd()
    {
        $collection = new ArrayCollection();
        $collection->add('foo');

        $this->assertEquals(['foo'], $collection->toArray());

        $collection->add('bar');

        $this->assertEquals(['foo', 'bar'], $collection->toArray());
    }

    /**
     * @group legacy
     */
    public function testSet()
    {
        $collection = new ArrayCollection(['foo']);
        $collection->set('foo', 1);

        $this->assertEquals(['foo', 'foo' => 1], $collection->toArray());

        $collection->set('foo', 2);

        $this->assertEquals(['foo', 'foo' => 2], $collection->toArray());
    }

    /**
     * @group legacy
     */
    public function testGet()
    {
        $collection = new ArrayCollection(['foo' => 1, 'bar']);

        $this->assertEquals(1, $collection->get('foo'));
        $this->assertEquals('bar', $collection->get(0));
        $this->assertNull($collection->get('bar'));
    }

    /**
     * @group legacy
     */
    public function testRemove()
    {
        $collection = new ArrayCollection(['foo' => 1, 'bar', 'baz']);

        $removed = $collection->remove('foo');
        $this->assertEquals(1, $removed);
        $this->assertEquals(['bar', 'baz'], $collection->toArray());

        $removed = $collection->remove('foo');
        $this->assertNull($removed);
        $this->assertEquals(['bar', 'baz'], $collection->toArray());

        $removed = $collection->remove(0);
        $this->assertEquals('bar', $removed);
        $this->assertEquals([1 => 'baz'], $collection->toArray());

        $removed = $collection->remove(1);
        $this->assertEquals('baz', $removed);
        $this->assertEquals([], $collection->toArray());
    }

    /**
     * @group legacy
     */
    public function testRemoveNulls()
    {
        $collection = new ArrayCollection(['foo' => null]);

        $removed = $collection->remove('nonExistantKey');
        $this->assertNull($removed);
        $this->assertEquals(['foo' => null], $collection->toArray());

        $removed = $collection->remove('foo');
        $this->assertNull($removed);
        $this->assertEquals([], $collection->toArray());
    }

    /**
     * @group legacy
     */
    public function testIsEmpty()
    {
        $collection = new ArrayCollection();
        $this->assertTrue($collection->isEmpty());

        $collection = new ArrayCollection([]);
        $this->assertTrue($collection->isEmpty());

        $collection = new ArrayCollection(['foo']);
        $this->assertFalse($collection->isEmpty());

        $collection = new ArrayCollection();
        $collection->add('foo');
        $this->assertFalse($collection->isEmpty());
    }

    /**
     * @group legacy
     */
    public function testContains()
    {
        $object = new \stdClass();
        $number = 3;
        $string = 'foo';

        $collection = new ArrayCollection([$object, $number, $string]);

        $this->assertTrue($collection->contains($object));
        $this->assertFalse($collection->contains(new \stdClass()));

        $this->assertTrue($collection->contains($number));
        $this->assertTrue($collection->contains(3));
        $this->assertFalse($collection->contains(3.000));

        $this->assertTrue($collection->contains($string));
        $this->assertTrue($collection->contains('foo'));
        $this->assertFalse($collection->contains('FOO'));
    }

    /**
     * @group legacy
     */
    public function testIndexOf()
    {
        $object = new \stdClass();
        $number = 3;
        $string = 'foo';

        $collection = new ArrayCollection([$object, $number, $string]);

        $this->assertTrue(0 === $collection->indexOf($object));
        $this->assertTrue(false === $collection->indexOf(new \stdClass()));

        $this->assertTrue(1 === $collection->indexOf($number));
        $this->assertTrue(1 === $collection->indexOf(3));
        $this->assertTrue(false === $collection->indexOf(3.000));

        $this->assertTrue(2 === $collection->indexOf($string));
        $this->assertTrue(2 === $collection->indexOf('foo'));
        $this->assertTrue(false === $collection->indexOf('FOO'));
    }

    /**
     * @group legacy
     */
    public function testContainsKey()
    {
        $collection = new ArrayCollection(['foo' => 1, 'bar']);

        $this->assertTrue($collection->containsKey('foo'));
        $this->assertTrue($collection->containsKey(0));

        $this->assertFalse($collection->containsKey('FOO'));
        $this->assertFalse($collection->containsKey(1));
    }

    public function testCount()
    {
        $collection = new ArrayCollection();
        $this->assertEquals(0, $collection->count());

        $collection = new ArrayCollection([]);
        $this->assertEquals(0, $collection->count());

        $collection = new ArrayCollection(['foo']);
        $this->assertEquals(1, $collection->count());

        $collection[] = 'bar';
        $this->assertEquals(2, $collection->count());

        unset($collection[0]);
        $this->assertEquals(1, $collection->count());
    }

    public function testOffsetExists()
    {
        $collection = new ArrayCollection(['foo' => 1, 'bar']);

        $this->assertTrue($collection->offsetExists('foo'));
        $this->assertTrue($collection->offsetExists(0));

        $this->assertFalse($collection->offsetExists('FOO'));
        $this->assertFalse($collection->offsetExists(1));
    }

    public function testOffsetGet()
    {
        $collection = new ArrayCollection(['foo' => 1, 'bar']);

        $this->assertEquals(1, $collection->offsetGet('foo'));
        $this->assertEquals('bar', $collection->offsetGet(0));
        $this->assertNull($collection->offsetGet('bar'));
    }

    public function testOffsetSet()
    {
        $collection = new ArrayCollection();
        $collection->offsetSet(null, 'foo');

        $this->assertEquals(['foo'], $collection->toArray());

        $collection->offsetSet(null, 'bar');

        $this->assertEquals(['foo', 'bar'], $collection->toArray());

        $collection = new ArrayCollection(['foo']);
        $collection->offsetSet('foo', 1);

        $this->assertEquals(['foo', 'foo' => 1], $collection->toArray());

        $collection->offsetSet('foo', 2);

        $this->assertEquals(['foo', 'foo' => 2], $collection->toArray());
    }

    public function testOffsetUnset()
    {
        $collection = new ArrayCollection(['foo' => 1, 'bar', 'baz']);

        $removed = $collection->offsetUnset('foo');
        $this->assertNull($removed);
        $this->assertEquals(['bar', 'baz'], $collection->toArray());

        $removed = $collection->offsetUnset('foo');
        $this->assertNull($removed);
        $this->assertEquals(['bar', 'baz'], $collection->toArray());

        $removed = $collection->offsetUnset(0);
        $this->assertNull($removed);
        $this->assertEquals([1 => 'baz'], $collection->toArray());

        $removed = $collection->offsetUnset(1);
        $this->assertNull($removed);
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

    /**
     * @group legacy
     */
    public function testReplaceWith()
    {
        $collection = new ArrayCollection(['foo' => 1, 'bar']);
        $replaced = $collection->replaceWith(['baz', 42]);

        $this->assertEquals($collection, $replaced);
        $this->assertEquals(['baz', 42], $collection->toArray());
        $this->assertEquals(['baz', 42], $replaced->toArray());
    }

    /**
     * @group legacy
     */
    public function testRemoveGaps()
    {
        $collection = new ArrayCollection(['', true, false, null, [], 0, '0', 1]);

        $collection->removeGaps();
        $this->assertEquals([1 => true, 7 => 1], $collection->toArray());
    }
}
