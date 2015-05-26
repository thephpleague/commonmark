<?php

/*
 * This is part of the webuni/commonmark-table-extension package.
 *
 * (c) Martin Hasoň <martin.hason@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webuni\CommonMark\TableExtension;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\HtmlElement;
use League\CommonMark\HtmlRendererInterface;

class TableRenderer implements BlockRendererInterface
{
    public function render(AbstractBlock $block, HtmlRendererInterface $htmlRenderer, $inTightList = false)
    {
        if (!($block instanceof Table)) {
            throw new \InvalidArgumentException('Incompatible block type: ' . get_class($block));
        }

        $separator = $htmlRenderer->getOption('inner_separator', "\n");

        return new HtmlElement(
            'table',
            array(),
            $separator . $htmlRenderer->renderBlocks($block->getChildren()) . $separator
        );
    }
}
