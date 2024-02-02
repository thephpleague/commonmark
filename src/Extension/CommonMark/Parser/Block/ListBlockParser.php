<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\CommonMark\Parser\Block;

use League\CommonMark\Extension\CommonMark\Node\Block\ListBlock;
use League\CommonMark\Extension\CommonMark\Node\Block\ListData;
use League\CommonMark\Extension\CommonMark\Node\Block\ListItem;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Parser\Block\AbstractBlockContinueParser;
use League\CommonMark\Parser\Block\BlockContinue;
use League\CommonMark\Parser\Block\BlockContinueParserInterface;
use League\CommonMark\Parser\Cursor;

final class ListBlockParser extends AbstractBlockContinueParser
{
    /** @psalm-readonly */
    private ListBlock $block;

    public function __construct(ListData $listData)
    {
        $this->block = new ListBlock($listData);
    }

    public function getBlock(): ListBlock
    {
        return $this->block;
    }

    public function isContainer(): bool
    {
        return true;
    }

    public function canContain(AbstractBlock $childBlock): bool
    {
        return $childBlock instanceof ListItem;
    }

    public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser): ?BlockContinue
    {
        // List blocks themselves don't have any markers, only list items. So try to stay in the list.
        // If there is a block start other than list item, canContain makes sure that this list is closed.
        return BlockContinue::at($cursor);
    }

    public function closeBlock(): void
    {
        $item = $this->block->firstChild();
        while ($item) {
            // check for non-final list item ending with blank line:
            if ($item->next() !== null && self::endsWithBlankLine($item)) {
                $this->block->setTight(false);
                break;
            }

            // recurse into children of list item, to see if there are spaces between any of them
            $subitem = $item->firstChild();
            while ($subitem) {
                if ($subitem->next() && self::endsWithBlankLine($subitem)) {
                    $this->block->setTight(false);
                    break 2;
                }

                $subitem = $subitem->next();
            }

            $item = $item->next();
        }

        $this->block->setEndLine($this->block->lastChild()->getEndLine());
    }

    private static function endsWithBlankLine(AbstractBlock $block): bool
    {
        return $block->next() !== null && $block->getEndLine() !== $block->next()->getStartLine() - 1;
    }
}
