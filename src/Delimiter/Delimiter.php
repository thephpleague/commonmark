<?php

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

namespace League\CommonMark\Delimiter;

use League\CommonMark\Node\Node;

class Delimiter
{
    /** @var string */
    protected $char;

    /** @var int */
    protected $numDelims;

    /** @var int */
    protected $origDelims;

    /** @var Node */
    protected $inlineNode;

    /** @var Delimiter|null */
    protected $previous;

    /** @var Delimiter|null */
    protected $next;

    /** @var bool */
    protected $canOpen;

    /** @var bool */
    protected $canClose;

    /** @var bool */
    protected $active;

    /** @var int|null */
    protected $index;

    /**
     * @param string   $char
     * @param int      $numDelims
     * @param Node     $node
     * @param bool     $canOpen
     * @param bool     $canClose
     * @param int|null $index
     */
    public function __construct(string $char, int $numDelims, Node $node, bool $canOpen, bool $canClose, ?int $index = null)
    {
        $this->char = $char;
        $this->numDelims = $numDelims;
        $this->origDelims = $numDelims;
        $this->inlineNode = $node;
        $this->canOpen = $canOpen;
        $this->canClose = $canClose;
        $this->active = true;
        $this->index = $index;
    }

    /**
     * @return bool
     */
    public function canClose(): bool
    {
        return $this->canClose;
    }

    /**
     * @param bool $canClose
     *
     * @return $this
     */
    public function setCanClose(bool $canClose)
    {
        $this->canClose = $canClose;

        return $this;
    }

    /**
     * @return bool
     */
    public function canOpen(): bool
    {
        return $this->canOpen;
    }

    /**
     * @param bool $canOpen
     *
     * @return $this
     */
    public function setCanOpen(bool $canOpen)
    {
        $this->canOpen = $canOpen;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return $this
     */
    public function setActive(bool $active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return string
     */
    public function getChar(): string
    {
        return $this->char;
    }

    /**
     * @param string $char
     *
     * @return $this
     */
    public function setChar(string $char)
    {
        $this->char = $char;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getIndex(): ?int
    {
        return $this->index;
    }

    /**
     * @param int|null $index
     *
     * @return $this
     */
    public function setIndex(?int $index)
    {
        $this->index = $index;

        return $this;
    }

    /**
     * @return Delimiter|null
     */
    public function getNext(): ?self
    {
        return $this->next;
    }

    /**
     * @param Delimiter|null $next
     *
     * @return $this
     */
    public function setNext(?self $next)
    {
        $this->next = $next;

        return $this;
    }

    /**
     * @return int
     */
    public function getNumDelims(): int
    {
        return $this->numDelims;
    }

    /**
     * @param int $numDelims
     *
     * @return $this
     */
    public function setNumDelims(int $numDelims)
    {
        $this->numDelims = $numDelims;

        return $this;
    }

    /**
     * @return int
     */
    public function getOrigDelims(): int
    {
        return $this->origDelims;
    }

    /**
     * @return Node
     */
    public function getInlineNode(): Node
    {
        return $this->inlineNode;
    }

    /**
     * @param Node $node
     *
     * @return $this
     */
    public function setInlineNode(Node $node)
    {
        $this->inlineNode = $node;

        return $this;
    }

    /**
     * @return Delimiter|null
     */
    public function getPrevious(): ?self
    {
        return $this->previous;
    }

    /**
     * @param Delimiter|null $previous
     *
     * @return $this
     */
    public function setPrevious(?self $previous)
    {
        $this->previous = $previous;

        return $this;
    }
}
