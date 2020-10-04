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
    public const TYPE_HEAD = 'th';
    public const TYPE_BODY = 'td';

    public const ALIGN_LEFT   = 'left';
    public const ALIGN_RIGHT  = 'right';
    public const ALIGN_CENTER = 'center';

    /**
     * @var string
     *
     * @psalm-readonly-allow-private-mutation
     */
    private $type = self::TYPE_BODY;

    /**
     * @var string|null
     *
     * @psalm-readonly-allow-private-mutation
     */
    private $align;

    public function __construct(string $type = self::TYPE_BODY, ?string $align = null)
    {
        parent::__construct();

        $this->type  = $type;
        $this->align = $align;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getAlign(): ?string
    {
        return $this->align;
    }

    public function setAlign(?string $align): void
    {
        $this->align = $align;
    }
}
