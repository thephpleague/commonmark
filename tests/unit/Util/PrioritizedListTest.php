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

use League\CommonMark\Util\PrioritizedList;
use PHPUnit\Framework\TestCase;

class PrioritizedListTest extends TestCase
{
    public function testAddSamePriorities()
    {
        $list = new PrioritizedList();
        $list->add('foo', 0);
        $list->add('bar', 0);

        $items = iterator_to_array($list->getIterator());

        $this->assertCount(2, $items);

        $this->assertEquals('foo', $items[0]);
        $this->assertEquals('bar', $items[1]);
    }

    public function testAddDifferentPriorities()
    {
        $list = new PrioritizedList();
        $list->add('foo', 0);
        $list->add('bar', 100);
        $list->add('baz', -20);

        $items = iterator_to_array($list->getIterator());

        $this->assertCount(3, $items);

        $this->assertEquals('bar', $items[0]);
        $this->assertEquals('foo', $items[1]);
        $this->assertEquals('baz', $items[2]);
    }
}
