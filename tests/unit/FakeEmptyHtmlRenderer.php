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

namespace League\CommonMark\Tests\Unit;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Element\BlockElement;
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\Inline\Element\InlineElement;

class FakeEmptyHtmlRenderer implements ElementRendererInterface
{
    /**
     * @param string     $option
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    public function getOption($option, $default = null)
    {
        return;
    }

    /**
     * @param string $string
     * @param bool   $preserveEntities
     *
     * @return string
     */
    public function escape($string, $preserveEntities = false)
    {
        return '';
    }

    /**
     * @param InlineElement[] $inlines
     *
     * @return string
     */
    public function renderInlines($inlines)
    {
        return '';
    }

    /**
     * @param BlockElement $block
     * @param bool         $inTightList
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function renderBlock(BlockElement $block, $inTightList = false)
    {
        return '';
    }

    /**
     * @param AbstractBlock[] $blocks
     * @param bool            $inTightList
     *
     * @return string
     */
    public function renderBlocks($blocks, $inTightList = false)
    {
        return '';
    }
}
