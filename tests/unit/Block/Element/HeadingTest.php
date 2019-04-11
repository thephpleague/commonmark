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

namespace League\CommonMark\Tests\Unit\Block\Element;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Element\Heading;
use League\CommonMark\Cursor;
use PHPUnit\Framework\TestCase;

class HeadingTest extends TestCase
{
    public function testConstructorAndGetLevel()
    {
        $heading = new Heading(1, 'CommonMark');

        $this->assertEquals(1, $heading->getLevel());
    }

    public function testCanContain()
    {
        $heading = new Heading(1, 'CommonMark');

        $block = $this->createMock(AbstractBlock::class);
        $this->assertFalse($heading->canContain($block));
    }

    public function testIsCode()
    {
        $heading = new Heading(1, 'CommonMark');

        $this->assertFalse($heading->isCode());
    }

    public function testMatchesNextLine()
    {
        $heading = new Heading(1, 'CommonMark');

        $cursor = $this->createMock(Cursor::class);
        $this->assertFalse($heading->matchesNextLine($cursor));
    }
}
