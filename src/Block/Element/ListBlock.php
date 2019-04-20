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

namespace League\CommonMark\Block\Element;

use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;

/**
 * @method children() AbstractBlock[]
 */
class ListBlock extends AbstractBlock
{
    const TYPE_UNORDERED = 'Bullet';
    const TYPE_ORDERED = 'Ordered';

    /**
     * @var bool
     */
    protected $tight = false;

    /**
     * @var ListData
     */
    protected $listData;

    public function __construct(ListData $listData)
    {
        $this->listData = $listData;
    }

    /**
     * @return ListData
     */
    public function getListData(): ListData
    {
        return $this->listData;
    }

    /**
     * @return bool
     */
    public function endsWithBlankLine(): bool
    {
        if ($this->lastLineBlank) {
            return true;
        }

        if ($this->hasChildren()) {
            return $this->lastChild() instanceof AbstractBlock && $this->lastChild()->endsWithBlankLine();
        }

        return false;
    }

    /**
     * Returns true if this block can contain the given block as a child node
     *
     * @param AbstractBlock $block
     *
     * @return bool
     */
    public function canContain(AbstractBlock $block): bool
    {
        return $block instanceof ListItem;
    }

    /**
     * Whether this is a code block
     *
     * @return bool
     */
    public function isCode(): bool
    {
        return false;
    }

    public function matchesNextLine(Cursor $cursor): bool
    {
        return true;
    }

    public function finalize(ContextInterface $context, int $endLineNumber)
    {
        parent::finalize($context, $endLineNumber);

        $this->tight = true; // tight by default

        foreach ($this->children() as $item) {
            if (!($item instanceof AbstractBlock)) {
                continue;
            }

            // check for non-final list item ending with blank line:
            if ($item->endsWithBlankLine() && $item !== $this->lastChild()) {
                $this->tight = false;
                break;
            }

            // Recurse into children of list item, to see if there are
            // spaces between any of them:
            foreach ($item->children() as $subItem) {
                if ($subItem instanceof AbstractBlock && $subItem->endsWithBlankLine() && ($item !== $this->lastChild() || $subItem !== $item->lastChild())) {
                    $this->tight = false;
                    break;
                }
            }
        }
    }

    /**
     * @return bool
     */
    public function isTight(): bool
    {
        return $this->tight;
    }

    /**
     * @param bool $tight
     *
     * @return $this
     */
    public function setTight(bool $tight): self
    {
        $this->tight = $tight;

        return $this;
    }
}
