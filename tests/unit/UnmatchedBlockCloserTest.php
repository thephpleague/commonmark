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

namespace League\CommonMark\Tests\Unit;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\ContextInterface;
use League\CommonMark\UnmatchedBlockCloser;
use PHPUnit\Framework\TestCase;

class UnmatchedBlockCloserTest extends TestCase
{
    public function testResetTip()
    {
        $tip = $this->getMockForAbstractClass(AbstractBlock::class);

        $context = $this->getMockForAbstractClass(ContextInterface::class);
        $context->method('getTip')->willReturn($tip);

        $closer = new UnmatchedBlockCloser($context);

        $closer->setLastMatchedContainer($tip);
        $closer->resetTip();

        $this->assertTrue($closer->areAllClosed());
    }

    public function testResetTipWithNullTip()
    {
        $this->expectException(\RuntimeException::class);

        $context = $this->getMockForAbstractClass(ContextInterface::class);
        $context->method('getTip')->willReturn(null);

        $closer = new UnmatchedBlockCloser($context);
        $closer->resetTip();
    }
}
