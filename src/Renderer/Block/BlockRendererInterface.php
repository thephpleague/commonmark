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

namespace League\CommonMark\Renderer\Block;

use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

interface BlockRendererInterface
{
    /**
     * @param AbstractBlock         $block
     * @param NodeRendererInterface $htmlRenderer
     * @param bool                  $inTightList
     *
     * @return HtmlElement|string|null
     */
    public function render(AbstractBlock $block, NodeRendererInterface $htmlRenderer, bool $inTightList = false);
}
