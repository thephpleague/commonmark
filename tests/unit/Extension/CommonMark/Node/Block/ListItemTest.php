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

namespace League\CommonMark\Tests\Unit\Extension\CommonMark\Node\Block;

use League\CommonMark\Extension\CommonMark\Node\Block\ListData;
use League\CommonMark\Extension\CommonMark\Node\Block\ListItem;
use PHPUnit\Framework\TestCase;

class ListItemTest extends TestCase
{
    public function testConstructorAndGetListData()
    {
        $listData = $this->createMock(ListData::class);
        $listBlock = new ListItem($listData);

        $this->assertSame($listData, $listBlock->getListData());
    }
}
