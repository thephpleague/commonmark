<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on stmd.js
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Block\Renderer;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Element\ListBlock;
use League\CommonMark\HtmlRenderer;

class ListBlockRenderer implements BlockRendererInterface
{
    /**
     * @param ListBlock $block
     * @param HtmlRenderer $htmlRenderer
     * @param bool $inTightList
     *
     * @return string
     */
    public function render(AbstractBlock $block, HtmlRenderer $htmlRenderer, $inTightList = false)
    {
        $listData = $block->getListData();
        $start = $listData->start ?: null;

        $tag = $listData->type == ListBlock::TYPE_UNORDERED ? 'ul' : 'ol';
        $attr = (!$start || $start == 1) ?
            array() : array('start' => (string)$start);

        return $htmlRenderer->inTags(
            $tag,
            $attr,
            $htmlRenderer->getOption('innerSeparator') . $htmlRenderer->renderBlocks(
                $block->getChildren(),
                $block->isTight()
            ) . $htmlRenderer->getOption('innerSeparator')
        );
    }
}
