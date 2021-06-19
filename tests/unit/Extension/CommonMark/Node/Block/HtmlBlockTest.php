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

namespace League\CommonMark\Tests\Unit\Extension\CommonMark\Node\Block;

use League\CommonMark\Extension\CommonMark\Node\Block\HtmlBlock;
use PHPUnit\Framework\TestCase;

final class HtmlBlockTest extends TestCase
{
    public function testConstructor(): void
    {
        $htmlBlock = new HtmlBlock(HtmlBlock::TYPE_3);
        $this->assertSame(HtmlBlock::TYPE_3, $htmlBlock->getType());
    }

    public function testGetSetType(): void
    {
        $htmlBlock = new HtmlBlock(HtmlBlock::TYPE_3);
        $htmlBlock->setType(HtmlBlock::TYPE_4);
        $this->assertSame(HtmlBlock::TYPE_4, $htmlBlock->getType());
    }

    public function testGetAndSetLiteral(): void
    {
        $htmlBlock = new HtmlBlock(HtmlBlock::TYPE_6_BLOCK_ELEMENT);
        $htmlBlock->setLiteral('<marquee>foo</marquee>');

        $this->assertSame('<marquee>foo</marquee>', $htmlBlock->getLiteral());
    }
}
