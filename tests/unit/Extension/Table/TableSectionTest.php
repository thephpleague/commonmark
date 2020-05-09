<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Extension\Table;

use League\CommonMark\Extension\Table\TableSection;
use PHPUnit\Framework\TestCase;

final class TableSectionTest extends TestCase
{
    public function testIsHeadAndIsBody(): void
    {
        $head = new TableSection(TableSection::TYPE_HEAD);
        $this->assertTrue($head->isHead());
        $this->assertFalse($head->isBody());

        $body = new TableSection(TableSection::TYPE_BODY);
        $this->assertFalse($body->isHead());
        $this->assertTrue($body->isBody());
    }
}
