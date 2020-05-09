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

namespace League\CommonMark\Extension\CommonMark\Node\Block;

class ListData
{
    /** @var int|null */
    public $start;

    /** @var int */
    public $padding = 0;

    /** @var string */
    public $type;

    /** @var string|null */
    public $delimiter;

    /** @var string|null */
    public $bulletChar;

    /** @var int */
    public $markerOffset;

    public function equals(ListData $data): bool
    {
        return $this->type === $data->type &&
            $this->delimiter === $data->delimiter &&
            $this->bulletChar === $data->bulletChar;
    }
}
