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

use League\CommonMark\Node\Block\AbstractBlock;

/**
 * @internal
 */
class UnmatchedBlockCloser
{
    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @var AbstractBlock
     */
    private $oldTip;

    /**
     * @var AbstractBlock
     */
    private $lastMatchedContainer;

    /**
     * @param ContextInterface $context
     */
    public function __construct(ContextInterface $context)
    {
        $this->context = $context;

        $this->resetTip();
    }

    public function setLastMatchedContainer(AbstractBlock $block): void
    {
        $this->lastMatchedContainer = $block;
    }

    public function closeUnmatchedBlocks(): void
    {
        $endLine = $this->context->getLineNumber() - 1;

        while ($this->oldTip !== $this->lastMatchedContainer) {
            /** @var AbstractBlock $oldTip */
            $oldTip = $this->oldTip->parent();
            $this->oldTip->finalize($this->context, $endLine);
            $this->oldTip = $oldTip;
        }
    }

    public function resetTip(): void
    {
        if ($this->context->getTip() === null) {
            throw new \RuntimeException('No tip to reset to');
        }

        $this->oldTip = $this->context->getTip();
    }

    public function areAllClosed(): bool
    {
        return $this->context->getTip() === $this->lastMatchedContainer;
    }
}
