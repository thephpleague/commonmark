<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Block\Element;

use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;
use League\CommonMark\Node\Node;
use League\CommonMark\Util\ArrayCollection;

/**
 * Block-level element
 */
abstract class AbstractBlock extends Node
{
    /**
     * Used for storage of arbitrary data.
     *
     * @var array
     */
    public $data = [];

    /**
     * @var ArrayCollection|string[]
     */
    protected $strings;

    /**
     * @var string
     */
    protected $finalStringContents = '';

    /**
     * @var bool
     */
    protected $open = true;

    /**
     * @var bool
     */
    protected $lastLineBlank = false;

    /**
     * @var int
     */
    protected $startLine;

    /**
     * @var int
     */
    protected $endLine;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->strings = new ArrayCollection();
    }

    /**
     * @return AbstractBlock|null
     */
    public function getParent()
    {
        return parent::getParent();
    }

    /**
     * @param Node $node
     */
    protected function setParent(Node $node)
    {
        if ($node && !$node instanceof self) {
            throw new \InvalidArgumentException('Parent of block must also be block (can not be inline)');
        }

        parent::setParent($node);
    }


    /**
     * @return bool
     */
    public function hasChildren()
    {
        return !is_null($this->firstChild);
    }

    /**
     * @param AbstractBlock $childBlock
     * @deprecated Instead of it use appendChild, prependChild or insertAfter, insertBefore of node itself.
     */
    public function addChild(AbstractBlock $childBlock)
    {
        $this->appendChild($childBlock);
    }

    /**
     * @param AbstractBlock $childBlock
     * @return bool
     * @deprecated Use detach method of node instead.
     */
    public function removeChild(AbstractBlock $childBlock)
    {
        if ($childBlock->parent === $this) {
            $childBlock->detach();

            return true;
        }

        return false;
    }

    /**
     * @param ContextInterface $context
     * @param AbstractBlock $original
     * @param AbstractBlock $replacement
     * @deprecated Use replaceWith method of original node
     */
    public function replaceChild(ContextInterface $context, AbstractBlock $original, AbstractBlock $replacement)
    {
        if ($original->parent === $this) {
            $original->replaceWith($replacement);
        } else {
            $this->appendChild($replacement);
        }

        if ($context->getTip() === $original) {
            $context->setTip($replacement);
        }
    }

    /**
     * Returns true if this block can contain the given block as a child node
     *
     * @param AbstractBlock $block
     *
     * @return bool
     */
    abstract public function canContain(AbstractBlock $block);

    /**
     * Returns true if block type can accept lines of text
     *
     * @return bool
     */
    abstract public function acceptsLines();

    /**
     * Whether this is a code block
     *
     * @return bool
     */
    abstract public function isCode();

    /**
     * @param Cursor $cursor
     *
     * @return bool
     */
    abstract public function matchesNextLine(Cursor $cursor);

    /**
     * @param ContextInterface $context
     * @param Cursor           $cursor
     */
    abstract public function handleRemainingContents(ContextInterface $context, Cursor $cursor);

    /**
     * @param int $startLine
     *
     * @return $this
     */
    public function setStartLine($startLine)
    {
        $this->startLine = $startLine;
        if (empty($this->endLine)) {
            $this->endLine = $startLine;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getStartLine()
    {
        return $this->startLine;
    }

    public function setEndLine($endLine)
    {
        $this->endLine = $endLine;

        return $this;
    }

    /**
     * @return int
     */
    public function getEndLine()
    {
        return $this->endLine;
    }

    /**
     * Whether the block ends with a blank line
     *
     * @return bool
     */
    public function endsWithBlankLine()
    {
        return $this->lastLineBlank;
    }

    /**
     * @return string[]
     */
    public function getStrings()
    {
        return $this->strings->toArray();
    }

    /**
     * @param string $line
     */
    public function addLine($line)
    {
        if (!$this->acceptsLines()) {
            throw new \LogicException('You cannot add lines to a block which cannot accept them');
        }

        $this->strings->add($line);
    }

    /**
     * Whether the block is open for modifications
     *
     * @return bool
     */
    public function isOpen()
    {
        return $this->open;
    }

    /**
     * Finalize the block; mark it closed for modification
     *
     * @param ContextInterface $context
     */
    public function finalize(ContextInterface $context)
    {
        if (!$this->open) {
            return; // TODO: Throw AlreadyClosedException?
        }

        $this->open = false;
        if ($context->getLineNumber() > $this->getStartLine()) {
            $this->endLine = $context->getLineNumber() - 1;
        } else {
            $this->endLine = $context->getLineNumber();
        }

        $context->setTip($context->getTip()->getParent());
    }

    /**
     * @return string
     */
    public function getStringContent()
    {
        return $this->finalStringContents;
    }

    /**
     * @param Cursor $cursor
     * @param int    $currentLineNumber
     *
     * @return $this
     */
    public function setLastLineBlank(Cursor $cursor, $currentLineNumber)
    {
        $this->lastLineBlank = $cursor->isBlank();

        $container = $this;
        while ($container->getParent()) {
            $container = $container->getParent();
            $container->lastLineBlank = false;
        }

        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getData($key, $default = null)
    {
        return array_key_exists($key, $this->data) ? $this->data[$key] : $default;
    }
}
