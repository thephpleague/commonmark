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

use League\CommonMark\Extension\CommonMark\Node\Block\ListBlock;
use League\CommonMark\Extension\CommonMark\Node\Block\ListData;
use PHPUnit\Framework\TestCase;

final class ListBlockTest extends TestCase
{
    public function testConstructorAndGetListData(): void
    {
        $listData  = $this->createMock(ListData::class);
        $listBlock = new ListBlock($listData);

        $this->assertSame($listData, $listBlock->getListData());
    }

    public function testSetTight(): void
    {
        $listData  = $this->createMock(ListData::class);
        $listBlock = new ListBlock($listData);

        $listBlock->setTight(true);
        $this->assertTrue($listBlock->isTight());

        $listBlock->setTight(false);
        $this->assertFalse($listBlock->isTight());
    }
}
