<?php

/*
 * This file is part of the league/commonmark-ext-task-list package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Ext\Autolink\Test;

use League\CommonMark\Ext\TaskList\TaskListItemMarker;
use PHPUnit\Framework\TestCase;

final class TaskListItemMarkerTest extends TestCase
{
    public function testIt()
    {
        $marker = new TaskListItemMarker(true);
        $this->assertTrue($marker->isChecked());

        $marker->setChecked(false);
        $this->assertFalse($marker->isChecked());
    }
}
