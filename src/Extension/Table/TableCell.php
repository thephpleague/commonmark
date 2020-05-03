<?php

declare(strict_types=1);

/*
 * This is part of the league/commonmark package.
 *
 * (c) Martin Hasoň <martin.hason@gmail.com>
 * (c) Webuni s.r.o. <info@webuni.cz>
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\Table;

use League\CommonMark\Node\Block\AbstractBlock;

final class TableCell extends AbstractBlock
{
    const TYPE_HEAD = 'th';
    const TYPE_BODY = 'td';

    const ALIGN_LEFT = 'left';
    const ALIGN_RIGHT = 'right';
    const ALIGN_CENTER = 'center';

    /** @var string */
    public $type = self::TYPE_BODY;

    /** @var string|null */
    public $align;

    public function __construct(string $type = self::TYPE_BODY, string $align = null)
    {
        $this->type = $type;
        $this->align = $align;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string|null
     */
    public function getAlign(): ?string
    {
        return $this->align;
    }

    /**
     * @param string|null $align
     */
    public function setAlign(?string $align): void
    {
        $this->align = $align;
    }
}
