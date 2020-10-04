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

use League\CommonMark\Node\Block\AbstractBlock;

class ListBlock extends AbstractBlock
{
    public const TYPE_BULLET  = 'bullet';
    public const TYPE_ORDERED = 'ordered';

    /** @var bool */
    protected $tight = false;

    /**
     * @var ListData
     *
     * @psalm-readonly
     */
    protected $listData;

    public function __construct(ListData $listData)
    {
        parent::__construct();

        $this->listData = $listData;
    }

    public function getListData(): ListData
    {
        return $this->listData;
    }

    public function isTight(): bool
    {
        return $this->tight;
    }

    public function setTight(bool $tight): void
    {
        $this->tight = $tight;
    }
}
