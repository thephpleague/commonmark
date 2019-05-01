<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Reference;

use League\CommonMark\Reference\Reference;
use League\CommonMark\Reference\ReferenceMap;
use PHPUnit\Framework\TestCase;

class ReferenceMapTest extends TestCase
{
    public function testAddNewReference()
    {
        $map = new ReferenceMap();

        $reference = new Reference('foo', 'bar', 'baz');
        $map->addReference($reference);

        $this->assertTrue($map->contains('foo'));
        $this->assertSame($reference, $map->getReference('foo'));
    }

    public function testUnicodeCaseFolding()
    {
        $map = new ReferenceMap();

        $reference = new Reference('ẞ', 'bar', 'baz');
        $map->addReference($reference);

        $this->assertTrue($map->contains('ẞ'));
        $this->assertTrue($map->contains('ß'));
        $this->assertTrue($map->contains('SS'));
        $this->assertTrue($map->contains('ss'));
    }

    public function testOverwriteReference()
    {
        $map = new ReferenceMap();

        $reference1 = new Reference('foo', 'bar', 'baz');
        $map->addReference($reference1);

        $reference2 = new Reference('foo', 'baz', 'baz');
        $map->addReference($reference2);

        $this->assertTrue($map->contains('foo'));
        $this->assertSame($reference2, $map->getReference('foo'));
        $this->assertCount(1, $map->listReferences());
    }

    public function testGetReferenceWhenNotExists()
    {
        $map = new ReferenceMap();

        $this->assertNull($map->getReference('foo'));
    }

    public function testListReferences()
    {
        $map = new ReferenceMap();

        $this->assertCount(0, $map->listReferences());

        $map->addReference(new Reference('foo', 'aaa', 'aaa'));
        $map->addReference(new Reference('bar', 'bbb', 'bbb'));

        $this->assertCount(2, $map->listReferences());
    }
}
