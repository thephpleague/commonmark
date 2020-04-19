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

namespace League\CommonMark\Extension\CommonMark\Renderer\Block;

use League\CommonMark\Extension\CommonMark\Node\Block\ListItem;
use League\CommonMark\Extension\TaskList\TaskListItemMarker;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Renderer\Block\BlockRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

final class ListItemRenderer implements BlockRendererInterface
{
    /**
     * @param ListItem              $block
     * @param NodeRendererInterface $htmlRenderer
     * @param bool                  $inTightList
     *
     * @return string
     */
    public function render(AbstractBlock $block, NodeRendererInterface $htmlRenderer, bool $inTightList = false)
    {
        if (!($block instanceof ListItem)) {
            throw new \InvalidArgumentException('Incompatible block type: ' . \get_class($block));
        }

        $contents = $htmlRenderer->renderBlocks($block->children(), $inTightList);
        if (\substr($contents, 0, 1) === '<' && !$this->startsTaskListItem($block)) {
            $contents = "\n" . $contents;
        }
        if (\substr($contents, -1, 1) === '>') {
            $contents .= "\n";
        }

        $attrs = $block->getData('attributes', []);

        $li = new HtmlElement('li', $attrs, $contents);

        return $li;
    }

    private function startsTaskListItem(ListItem $block): bool
    {
        $firstChild = $block->firstChild();

        return $firstChild instanceof Paragraph && $firstChild->firstChild() instanceof TaskListItemMarker;
    }
}
