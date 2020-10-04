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

final class TableSection extends AbstractBlock
{
    public const TYPE_HEAD = 'thead';
    public const TYPE_BODY = 'tbody';

    /**
     * @var string
     *
     * @psalm-readonly
     */
    private $type;

    public function __construct(string $type = self::TYPE_BODY)
    {
        parent::__construct();

        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isHead(): bool
    {
        return $this->type === self::TYPE_HEAD;
    }

    public function isBody(): bool
    {
        return $this->type === self::TYPE_BODY;
    }
}
