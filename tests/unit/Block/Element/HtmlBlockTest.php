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
use League\CommonMark\Block\Element\HtmlBlock;
use PHPUnit\Framework\TestCase;

class HtmlBlockTest extends TestCase
{
    public function testConstructor()
    {
        $htmlBlock = new HtmlBlock(HtmlBlock::TYPE_3);
        $this->assertSame(HtmlBlock::TYPE_3, $htmlBlock->getType());
    }

    public function testGetSetType()
    {
        $htmlBlock = new HtmlBlock(HtmlBlock::TYPE_3);
        $htmlBlock->setType(HtmlBlock::TYPE_4);
        $this->assertSame(HtmlBlock::TYPE_4, $htmlBlock->getType());
    }

    public function testCanContain()
    {
        $htmlBlock = new HtmlBlock(HtmlBlock::TYPE_3);
        $block = $this->createMock(AbstractBlock::class);
        $this->assertFalse($htmlBlock->canContain($block));
    }

    public function testIsCode()
    {
        $htmlBlock = new HtmlBlock(HtmlBlock::TYPE_3);
        $this->assertTrue($htmlBlock->isCode());
    }
}
