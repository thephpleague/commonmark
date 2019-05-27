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

use League\CommonMark\Inline\Element\AbstractStringContainer;

final class Delimiter implements DelimiterInterface
{
    /** @var string */
    private $char;

    /** @var int */
    private $length;

    /** @var int */
    private $originalLength;

    /** @var AbstractStringContainer */
    private $inlineNode;

    /** @var DelimiterInterface|null */
    private $previous;

    /** @var DelimiterInterface|null */
    private $next;

    /** @var bool */
    private $canOpen;

    /** @var bool */
    private $canClose;

    /** @var bool */
    private $active;

    /** @var int|null */
    private $index;

    /**
     * @param string                  $char
     * @param int                     $numDelims
     * @param AbstractStringContainer $node
     * @param bool                    $canOpen
     * @param bool                    $canClose
     * @param int|null                $index
     */
    public function __construct(string $char, int $numDelims, AbstractStringContainer $node, bool $canOpen, bool $canClose, ?int $index = null)
    {
        $this->char = $char;
        $this->length = $numDelims;
        $this->originalLength = $numDelims;
        $this->inlineNode = $node;
        $this->canOpen = $canOpen;
        $this->canClose = $canClose;
        $this->active = true;
        $this->index = $index;
    }

    /**
     * {@inheritdoc}
     */
    public function canClose(): bool
    {
        return $this->canClose;
    }

    /**
     * {@inheritdoc}
     */
    public function setCanClose(bool $canClose)
    {
        $this->canClose = $canClose;
    }

    /**
     * {@inheritdoc}
     */
    public function canOpen(): bool
    {
        return $this->canOpen;
    }

    /**
     * {@inheritdoc}
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * {@inheritdoc}
     */
    public function setActive(bool $active)
    {
        $this->active = $active;
    }

    /**
     * {@inheritdoc}
     */
    public function getChar(): string
    {
        return $this->char;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndex(): ?int
    {
        return $this->index;
    }

    /**
     * {@inheritdoc}
     */
    public function getNext(): ?DelimiterInterface
    {
        return $this->next;
    }

    /**
     * {@inheritdoc}
     */
    public function setNext(?DelimiterInterface $next)
    {
        $this->next = $next;
    }

    /**
     * {@inheritdoc}
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * {@inheritdoc}
     */
    public function setLength(int $length)
    {
        $this->length = $length;
    }

    /**
     * {@inheritdoc}
     */
    public function getOriginalLength(): int
    {
        return $this->originalLength;
    }

    /**
     * {@inheritdoc}
     */
    public function getInlineNode(): AbstractStringContainer
    {
        return $this->inlineNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrevious(): ?DelimiterInterface
    {
        return $this->previous;
    }

    /**
     * {@inheritdoc}
     */
    public function setPrevious(?DelimiterInterface $previous): DelimiterInterface
    {
        $this->previous = $previous;

        return $this;
    }
}
