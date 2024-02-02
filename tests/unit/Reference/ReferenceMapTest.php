<?php

declare(strict_types=1);

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

final class ReferenceMapTest extends TestCase
{
    public function testAddNewReference(): void
    {
        $map = new ReferenceMap();

        $reference = new Reference('foo', 'bar', 'baz');
        $map->add($reference);

        $this->assertTrue($map->contains('foo'));
        $this->assertSame($reference, $map->get('foo'));
    }

    /**
     * @dataProvider provideLabelsForCaseFoldingTest
     */
    public function testUnicodeCaseFolding(string $label): void
    {
        $map = new ReferenceMap();

        $reference = new Reference($label, 'bar', 'baz');
        $map->add($reference);

        $this->assertTrue($map->contains('ẞ'));
        $this->assertTrue($map->contains('ß'));
        $this->assertTrue($map->contains('SS'));
        $this->assertTrue($map->contains('ss'));
    }

    /**
     * @return iterable<array<string>>
     */
    public static function provideLabelsForCaseFoldingTest(): iterable
    {
        yield ['ẞ'];
        yield ['ß'];
        yield ['SS'];
        yield ['ss'];
    }

    public function testOverwriteReference(): void
    {
        $map = new ReferenceMap();

        $reference1 = new Reference('foo', 'bar', 'baz');
        $map->add($reference1);

        $reference2 = new Reference('foo', 'baz', 'baz');
        $map->add($reference2);

        $this->assertTrue($map->contains('foo'));
        $this->assertSame($reference2, $map->get('foo'));
        $this->assertCount(1, $map);
    }

    public function testGetReferenceWhenNotExists(): void
    {
        $map = new ReferenceMap();

        $this->assertNull($map->get('foo'));
    }

    public function testGetIterator(): void
    {
        $map = new ReferenceMap();

        $map->add($ref1 = new Reference('foo', 'aaa', 'aaa'));
        $map->add($ref2 = new Reference('bar', 'bbb', 'bbb'));

        $references = \iterator_to_array($map->getIterator());

        $this->assertCount(2, $references);
        $this->assertContains($ref1, $references);
        $this->assertContains($ref2, $references);
    }

    public function testCount(): void
    {
        $map = new ReferenceMap();

        $map->add($ref1 = new Reference('foo', 'aaa', 'aaa'));
        $map->add($ref2 = new Reference('bar', 'bbb', 'bbb'));

        $this->assertSame(2, $map->count());
        $this->assertCount(2, $map);
    }
}
