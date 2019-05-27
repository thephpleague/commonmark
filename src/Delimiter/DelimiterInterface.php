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

interface DelimiterInterface
{
    /**
     * @return bool
     */
    public function canClose(): bool;

    /**
     * @param bool $canClose
     *
     * @return $this
     */
    public function setCanClose(bool $canClose): DelimiterInterface;

    /**
     * @return bool
     */
    public function canOpen(): bool;

    /**
     * @return bool
     */
    public function isActive(): bool;

    /**
     * @param bool $active
     *
     * @return $this
     */
    public function setActive(bool $active): DelimiterInterface;

    /**
     * @return string
     */
    public function getChar(): string;

    /**
     * @return int|null
     */
    public function getIndex(): ?int;

    /**
     * @return DelimiterInterface|null
     */
    public function getNext(): ?DelimiterInterface;

    /**
     * @param DelimiterInterface|null $next
     *
     * @return $this
     */
    public function setNext(?DelimiterInterface $next): DelimiterInterface;

    /**
     * @return int
     */
    public function getLength(): int;

    /**
     * @param int $length
     *
     * @return $this
     */
    public function setLength(int $length): DelimiterInterface;

    /**
     * @return int
     */
    public function getOriginalLength(): int;

    /**
     * @return AbstractStringContainer
     */
    public function getInlineNode(): AbstractStringContainer;

    /**
     * @return DelimiterInterface|null
     */
    public function getPrevious(): ?DelimiterInterface;

    /**
     * @param DelimiterInterface|null $previous
     *
     * @return $this
     */
    public function setPrevious(?DelimiterInterface $previous): DelimiterInterface;
}
