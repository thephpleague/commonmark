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

use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use PHPUnit\Framework\TestCase;

final class FencedCodeTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $fencedCode = new FencedCode(3, '~', 4);

        $this->assertEquals(3, $fencedCode->getLength());
        $this->assertEquals('~', $fencedCode->getChar());
        $this->assertEquals(4, $fencedCode->getOffset());
    }

    public function testSetChar(): void
    {
        $fencedCode = new FencedCode(3, '~', 4);

        $fencedCode->setChar('`');
        $this->assertEquals('`', $fencedCode->getChar());
    }

    public function testSetLength(): void
    {
        $fencedCode = new FencedCode(3, '~', 4);

        $fencedCode->setLength(4);
        $this->assertEquals(4, $fencedCode->getLength());
    }

    public function testSetOffset(): void
    {
        $fencedCode = new FencedCode(3, '~', 4);

        $fencedCode->setOffset(6);
        $this->assertEquals(6, $fencedCode->getOffset());
    }

    public function testGetAndSetInfo(): void
    {
        $fencedCode = new FencedCode(3, '~', 4);
        $fencedCode->setInfo('ruby startline=3');

        $this->assertSame('ruby startline=3', $fencedCode->getInfo());
        $this->assertSame(['ruby', 'startline=3'], $fencedCode->getInfoWords());
    }
}
