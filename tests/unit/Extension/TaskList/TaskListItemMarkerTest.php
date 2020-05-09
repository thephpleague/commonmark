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

namespace League\CommonMark\Tests\Unit\Extension\TaskList;

use League\CommonMark\Extension\TaskList\TaskListItemMarker;
use PHPUnit\Framework\TestCase;

final class TaskListItemMarkerTest extends TestCase
{
    public function testIt(): void
    {
        $marker = new TaskListItemMarker(true);
        $this->assertTrue($marker->isChecked());

        $marker->setChecked(false);
        $this->assertFalse($marker->isChecked());
    }
}
