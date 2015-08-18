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

use League\CommonMark\Block\Element\BlockElement;
use League\CommonMark\Inline\Element\InlineElement;

/**
 * Renders a parsed AST to a string representation
 */
interface ElementRendererInterface
{
    /**
     * @param string $option
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getOption($option, $default = null);

    /**
     * @param string $string
     * @param bool   $preserveEntities
     *
     * @return string
     */
    public function escape($string, $preserveEntities = false);

    /**
     * @param InlineElement[] $inlines
     *
     * @return string
     */
    public function renderInlines($inlines);

    /**
     * @param BlockElement $block
     * @param bool          $inTightList
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function renderBlock(BlockElement $block, $inTightList = false);

    /**
     * @param BlockElement[] $blocks
     * @param bool            $inTightList
     *
     * @return string
     */
    public function renderBlocks($blocks, $inTightList = false);
}
