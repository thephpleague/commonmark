<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark;

class CursorState
{
    /**
     * @var string
     */
    private $line;

    /**
     * @var int
     */
    private $length;

    /**
     * @var int
     */
    private $currentPosition;

    /**
     * @var int
     */
    private $previousPosition;

    /**
     * @var int|null
     */
    private $nextNonSpaceCache;

    /**
     * @var int
     */
    private $indent;

    /**
     * @var int
     */
    private $column;

    /**
     * @var bool
     */
    private $partiallyConsumedTab;

    /**
     * @param string   $line
     * @param int      $length
     * @param int      $currentPosition
     * @param int      $previousPosition
     * @param int|null $nextNonSpaceCache
     * @param int      $indent
     * @param int      $column
     * @param bool     $partiallyConsumedTab
     */
    public function __construct($line, $length, $currentPosition, $previousPosition, $nextNonSpaceCache, $indent, $column, $partiallyConsumedTab)
    {
        $this->line = $line;
        $this->length = $length;
        $this->currentPosition = $currentPosition;
        $this->previousPosition = $previousPosition;
        $this->nextNonSpaceCache = $nextNonSpaceCache;
        $this->indent = $indent;
        $this->column = $column;
        $this->partiallyConsumedTab = $partiallyConsumedTab;
    }

    /**
     * @return int
     */
    public function getCurrentPosition()
    {
        return $this->currentPosition;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @return string
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @return int
     */
    public function getPreviousPosition()
    {
        return $this->previousPosition;
    }

    /**
     * @return int|null
     *
     * @deprecated Use getNextNonSpaceCache() instead
     */
    public function getFirstNonSpaceCache()
    {
        @trigger_error('CursorState::getFirstNonSpaceCache() will be removed in a future 0.x release.  Use getNextNonSpaceCache() instead. See https://github.com/thephpleague/commonmark/issues/280', E_USER_DEPRECATED);

        return $this->nextNonSpaceCache;
    }

    /**
     * @return int|null
     */
    public function getNextNonSpaceCache()
    {
        return $this->nextNonSpaceCache;
    }

    /**
     * @return int
     */
    public function getIndent()
    {
        return $this->indent;
    }

    /**
     * @return int
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @return bool
     */
    public function getPartiallyConsumedTab()
    {
        return $this->partiallyConsumedTab;
    }
}
