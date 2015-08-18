<?php

namespace League\CommonMark\Block\Element;

use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;

interface BlockElement
{
    /**
     * Returns true if this block can contain the given block as a child node
     *
     * @param BlockElement $block
     *
     * @return bool
     */
    public function canContain(BlockElement $block);

    /**
     * Returns true if block type can accept lines of text
     *
     * @return bool
     */
    public function acceptsLines();

    /**
     * Whether this is a code block
     *
     * @return bool
     */
    public function isCode();

    /**
     * @param Cursor $cursor
     *
     * @return bool
     */
    public function matchesNextLine(Cursor $cursor);

    /**
     * @param ContextInterface $context
     * @param Cursor           $cursor
     */
    public function handleRemainingContents(ContextInterface $context, Cursor $cursor);

    /**
     * @return string
     */
    public function getStringContent();

    /**
     * Finalize the block; mark it closed for modification
     *
     * @param ContextInterface $context
     */
    public function finalize(ContextInterface $context);

    /**
     * Whether the block is open for modifications
     *
     * @return bool
     */
    public function isOpen();
}
