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
use League\CommonMark\Block\Element\Document;
use League\CommonMark\Block\Element\FencedCode;
use League\CommonMark\Context;
use PHPUnit\Framework\TestCase;

class FencedCodeTest extends TestCase
{
    public function testConstructorAndGetters()
    {
        $fencedCode = new FencedCode(3, '~', 4);

        $this->assertEquals(3, $fencedCode->getLength());
        $this->assertEquals('~', $fencedCode->getChar());
        $this->assertEquals(4, $fencedCode->getOffset());
    }

    public function testSetChar()
    {
        $fencedCode = new FencedCode(3, '~', 4);

        $fencedCode->setChar('`');
        $this->assertEquals('`', $fencedCode->getChar());
    }

    public function testSetLength()
    {
        $fencedCode = new FencedCode(3, '~', 4);

        $fencedCode->setLength(4);
        $this->assertEquals(4, $fencedCode->getLength());
    }

    public function testSetOffset()
    {
        $fencedCode = new FencedCode(3, '~', 4);

        $fencedCode->setOffset(6);
        $this->assertEquals(6, $fencedCode->getOffset());
    }

    public function testCanContain()
    {
        $fencedCode = new FencedCode(3, '~', 4);

        $block = $this->createMock(AbstractBlock::class);
        $this->assertFalse($fencedCode->canContain($block));
    }

    public function testIsCode()
    {
        $fencedCode = new FencedCode(3, '~', 4);

        $this->assertTrue($fencedCode->isCode());
    }

    public function testFinalizeAndGetInfo()
    {
        $fencedCode = new FencedCode(3, '~', 4);
        $fencedCode->addLine('ruby startline=3');
        $fencedCode->addLine('hello world');

        $context = $this->createMock(Context::class);
        $context->method('getTip')->willReturn(new Document());

        $fencedCode->finalize($context, 7);

        $this->assertEquals('ruby startline=3', $fencedCode->getInfo());
        $this->assertEquals(['ruby', 'startline=3'], $fencedCode->getInfoWords());
        $this->assertEquals("hello world\n", $fencedCode->getStringContent());
    }
}
