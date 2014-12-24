<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmarkjs)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Block\Renderer;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Element\Header;
use League\CommonMark\HtmlElement;
use League\CommonMark\HtmlRenderer;

class HeaderRenderer implements BlockRendererInterface
{
    /**
     * @param Header $block
     * @param HtmlRenderer $htmlRenderer
     * @param bool $inTightList
     *
     * @return HtmlElement
     */
    public function render(AbstractBlock $block, HtmlRenderer $htmlRenderer, $inTightList = false)
    {
        if (!($block instanceof Header)) {
            throw new \InvalidArgumentException('Incompatible block type: ' . get_class($block));
        }

        $tag = 'h' . $block->getLevel();

        return new HtmlElement($tag, array(), $htmlRenderer->renderInlines($block->getInlines()));
    }
}
