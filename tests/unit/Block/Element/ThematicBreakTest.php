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
use League\CommonMark\Block\Element\ThematicBreak;
use League\CommonMark\Cursor;
use PHPUnit\Framework\TestCase;

class ThematicBreakTest extends TestCase
{
    public function testCanContain()
    {
        $thematicBreak = new ThematicBreak();
        $block = $this->createMock(AbstractBlock::class);
        $this->assertFalse($thematicBreak->canContain($block));
    }

    public function testIsCode()
    {
        $thematicBreak = new ThematicBreak();
        $this->assertFalse($thematicBreak->isCode());
    }

    public function testMatchesNextLine()
    {
        $thematicBreak = new ThematicBreak();
        $cursor = $this->createMock(Cursor::class);
        $this->assertFalse($thematicBreak->matchesNextLine($cursor));
    }
}
