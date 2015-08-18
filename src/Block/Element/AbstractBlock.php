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
use League\CommonMark\Node\NodeContainer;
use League\CommonMark\Util\ArrayCollection;

/**
 * Block-level element
 */
abstract class AbstractBlock extends NodeContainer implements BlockElement
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
     * @param NodeContainer $node
     */
    protected function setParent(NodeContainer $node)
    {
        if ($node && !$node instanceof self) {
            throw new \InvalidArgumentException('Parent of block must also be block (can not be inline)');
        }

        parent::setParent($node);
    }

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

        $context->setTip($context->getTip()->parent());
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
        while ($container->parent()) {
            $container = $container->parent();
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
