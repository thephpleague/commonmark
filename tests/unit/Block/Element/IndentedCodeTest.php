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
use League\CommonMark\Block\Element\IndentedCode;
use PHPUnit\Framework\TestCase;

class IndentedCodeTest extends TestCase
{
    public function testCanContain()
    {
        $indentedCode = new IndentedCode();

        $block = $this->createMock(AbstractBlock::class);
        $this->assertFalse($indentedCode->canContain($block));
    }

    public function testIsCode()
    {
        $indentedCode = new IndentedCode();

        $this->assertTrue($indentedCode->isCode());
    }
}
