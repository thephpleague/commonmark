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

namespace League\CommonMark;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Element\AbstractInlineContainer as AbstractBlockInlineContainer;
use League\CommonMark\Block\Element\Document;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Inline\Element\AbstractInlineContainer;

/**
 * ElementTraverser is a element traverser.
 *
 * It visits all elements and their children and calls the given visitor for each.
 */
class ElementTraverser
{
    private $environment;
    private $context;

    /**
     * Constructor.
     *
     * @param Environment $environment
     * @param ElementVisitorInterface[] $visitors
     */
    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
        $this->context = new Context(new Document(), $environment);
    }

    /**
     * Traverses a node and calls the registered visitors.
     *
     * @param AbstractBlock $block
     *
     * @return AbstractBlock
     */
    public function traverseBlock(AbstractBlock $block)
    {
        foreach ($this->environment->getElementVisitors() as $visitor) {
            $block = $this->traverseBlockForVisitor($visitor, $block);
        }

        return $block;
    }

    private function traverseBlockForVisitor(ElementVisitorInterface $visitor, AbstractBlock $block)
    {
        $block = $visitor->enterBlock($block, $this->environment);

        if ($block instanceof AbstractBlockInlineContainer) {
            $inlines = [];
            foreach ($block->getInlines() as $inline) {
                $newInline = $this->traverseInlineForVisitor($visitor, $inline);
                if (false !== $newInline) {
                    $inlines[] = $newInline;
                }
            }
            $block->setInlines($inlines);
        }

        foreach ($block->getChildren() as $child) {
            $newChild = $this->traverseBlockForVisitor($visitor, $child);
            if (false === $newChild) {
                $block->removeChild($child);
            } elseif ($newChild instanceof AbstractBlock && $child !== $newChild) {
                $block->replaceChild($this->context, $child, $newChild);
            }
        }

        return $visitor->leaveBlock($block, $this->environment);
    }

    private function traverseInlineForVisitor(ElementVisitorInterface $visitor, AbstractInline $inline)
    {
        $inline = $visitor->enterInline($inline, $this->environment);

        if ($inline instanceof AbstractInlineContainer) {
            $children = [];
            foreach ($inline->getChildren() as $child) {
                $newChild = $this->traverseInlineForVisitor($visitor, $child);
                if (false !== $newChild) {
                    $children[] = $newChild;
                }
            }
            $inline->setChildren($children);
        }

        return $visitor->leaveInline($inline, $this->environment);
    }
}
