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
use League\CommonMark\Node\StringContainerInterface;

class FencedCode extends AbstractBlock implements StringContainerInterface
{
    /** @var string|null */
    protected $info;

    /** @var string */
    protected $literal = '';

    /** @var int */
    protected $length;

    /** @var string */
    protected $char;

    /** @var int */
    protected $offset;

    public function __construct(int $length, string $char, int $offset)
    {
        $this->length = $length;
        $this->char   = $char;
        $this->offset = $offset;
    }

    public function getInfo(): ?string
    {
        return $this->info;
    }

    /**
     * @return string[]
     */
    public function getInfoWords(): array
    {
        return \preg_split('/\s+/', $this->info ?? '') ?: [];
    }

    public function setInfo(string $info): void
    {
        $this->info = $info;
    }

    public function getLiteral(): string
    {
        return $this->literal;
    }

    public function setLiteral(string $literal): void
    {
        $this->literal = $literal;
    }

    public function getChar(): string
    {
        return $this->char;
    }

    /**
     * @return $this
     */
    public function setChar(string $char): self
    {
        $this->char = $char;

        return $this;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * @return $this
     */
    public function setLength(int $length): self
    {
        $this->length = $length;

        return $this;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @return $this
     */
    public function setOffset(int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }
}
