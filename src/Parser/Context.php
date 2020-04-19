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

namespace League\CommonMark\Parser;

use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Node\Block\Document;
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

    public function setNextLine(string $line): void
    {
        ++$this->lineNumber;
        $this->line = $line;
    }

    public function getDocument(): Document
    {
        return $this->doc;
    }

    public function getTip(): ?AbstractBlock
    {
        return $this->tip;
    }

    public function setTip(?AbstractBlock $block): void
    {
        $this->tip = $block;
    }

    public function getLineNumber(): int
    {
        return $this->lineNumber;
    }

    public function getLine(): string
    {
        return $this->line;
    }

    public function getBlockCloser(): UnmatchedBlockCloser
    {
        return $this->blockCloser;
    }

    public function getContainer(): AbstractBlock
    {
        return $this->container;
    }

    public function setContainer(AbstractBlock $container): void
    {
        $this->container = $container;
    }

    public function addBlock(AbstractBlock $block): void
    {
        $this->blockCloser->closeUnmatchedBlocks();
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

    public function replaceContainerBlock(AbstractBlock $replacement): void
    {
        $this->blockCloser->closeUnmatchedBlocks();
        $this->container->replaceWith($replacement);

        if ($this->tip === $this->container) {
            $this->tip = $replacement;
        }

        $this->container = $replacement;
    }

    public function getBlocksParsed(): bool
    {
        return $this->blocksParsed;
    }

    public function setBlocksParsed(bool $bool): void
    {
        $this->blocksParsed = $bool;
    }

    public function getReferenceParser(): ReferenceParser
    {
        return $this->referenceParser;
    }
}
