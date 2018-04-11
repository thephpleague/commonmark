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

    /**
     * @param AbstractBlock $block
     */
    public function setLastMatchedContainer(AbstractBlock $block)
    {
        $this->lastMatchedContainer = $block;
    }

    public function closeUnmatchedBlocks()
    {
        $endLine = $this->context->getLineNumber() - 1;

        while ($this->oldTip !== $this->lastMatchedContainer) {
            $oldTip = $this->oldTip->parent();
            $this->oldTip->finalize($this->context, $endLine);
            $this->oldTip = $oldTip;
        }
    }

    public function resetTip()
    {
        $this->oldTip = $this->context->getTip();
    }

    /**
     * @return bool
     */
    public function areAllClosed()
    {
        return $this->context->getTip() === $this->lastMatchedContainer;
    }
}
