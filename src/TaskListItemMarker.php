<?php

/*
 * This file is part of the league/commonmark-ext-task-list package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Ext\TaskList;

use League\CommonMark\Inline\Element\AbstractInline;

final class TaskListItemMarker extends AbstractInline
{
    protected $checked = false;

    public function __construct(bool $isCompleted)
    {
        $this->checked = $isCompleted;
    }

    public function isChecked(): bool
    {
        return $this->checked;
    }

    public function setChecked(bool $checked): self
    {
        $this->checked = $checked;

        return $this;
    }
}
