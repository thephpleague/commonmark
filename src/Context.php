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

namespace League\CommonMark;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Element\Document;
use League\CommonMark\Reference\ReferenceParser;

/**
 * Maintains the current state of the Markdown parser engine
 */
class Context implements ContextInterface
{
    /**
     * @var EnvironmentInterface
     */
    protected $environment;

    /**
     * @var Document
     */
    protected $doc;

    /**
     * @var AbstractBlock|null
     */
    protected $tip;

    /**
     * @var AbstractBlock
     */
    protected $container;

    /**
     * @var int
     */
    protected $lineNumber;

    /**
     * @var string
     */
    protected $line;

    /**
     * @var UnmatchedBlockCloser
     */
    protected $blockCloser;

    /**
     * @var bool
     */
    protected $blocksParsed = false;

    /**
     * @var ReferenceParser
     */
    protected $referenceParser;

    public function __construct(Document $document, EnvironmentInterface $environment)
    {
        $this->doc = $document;
        $this->tip = $this->doc;
        $this->container = $this->doc;

        $this->environment = $environment;

        $this->referenceParser = new ReferenceParser($document->getReferenceMap());

        $this->blockCloser = new UnmatchedBlockCloser($this);
    }

    /**
     * @param string $line
     */
    public function setNextLine(string $line)
    {
        ++$this->lineNumber;
        $this->line = $line;
    }

    /**
     * @return Document
     */
    public function getDocument(): Document
    {
        return $this->doc;
    }

    /**
     * @return AbstractBlock|null
     */
    public function getTip(): ?AbstractBlock
    {
        return $this->tip;
    }

    /**
     * @param AbstractBlock|null $block
     *
     * @return $this
     */
    public function setTip(?AbstractBlock $block)
    {
        $this->tip = $block;

        return $this;
    }

    /**
     * @return int
     */
    public function getLineNumber(): int
    {
        return $this->lineNumber;
    }

    /**
     * @return string
     */
    public function getLine(): string
    {
        return $this->line;
    }

    /**
     * Finalize and close any unmatched blocks
     *
     * @return UnmatchedBlockCloser
     */
    public function getBlockCloser(): UnmatchedBlockCloser
    {
        return $this->blockCloser;
    }

    /**
     * @return AbstractBlock
     */
    public function getContainer(): AbstractBlock
    {
        return $this->container;
    }

    /**
     * @param AbstractBlock $container
     *
     * @return $this
     */
    public function setContainer(AbstractBlock $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * @param AbstractBlock $block
     */
    public function addBlock(AbstractBlock $block)
    {
        $this->getBlockCloser()->closeUnmatchedBlocks();
        $block->setStartLine($this->lineNumber);

        while ($this->tip !== null && !$this->tip->canContain($block)) {
            $this->tip->finalize($this, $this->lineNumber);
        }

        // This should always be true
        if ($this->tip !== null) {
            $this->tip->appendChild($block);
        }

        $this->tip = $block;
        $this->container = $block;
    }

    /**
     * @param AbstractBlock $replacement
     */
    public function replaceContainerBlock(AbstractBlock $replacement)
    {
        $this->getBlockCloser()->closeUnmatchedBlocks();
        $this->getContainer()->replaceWith($replacement);

        if ($this->getTip() === $this->getContainer()) {
            $this->setTip($replacement);
        }

        $this->setContainer($replacement);
    }

    /**
     * @return bool
     */
    public function getBlocksParsed(): bool
    {
        return $this->blocksParsed;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setBlocksParsed(bool $bool)
    {
        $this->blocksParsed = $bool;

        return $this;
    }

    /**
     * @return ReferenceParser
     */
    public function getReferenceParser(): ReferenceParser
    {
        return $this->referenceParser;
    }
}
