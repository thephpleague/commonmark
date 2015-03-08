<?php

namespace League\CommonMark\Tests;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\HtmlRendererInterface;
use League\CommonMark\Inline\Element\AbstractInline;

class FakeEmptyHtmlRenderer implements HtmlRendererInterface
{
    /**
     * @param string $option
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    public function getOption($option, $default = null)
    {
        return null;
    }

    /**
     * @param string $string
     * @param bool $preserveEntities
     *
     * @return string
     */
    public function escape($string, $preserveEntities = false)
    {
        return '';
    }

    /**
     * @param AbstractInline[] $inlines
     *
     * @return string
     */
    public function renderInlines($inlines)
    {
        return '';
    }

    /**
     * @param AbstractBlock $block
     * @param bool $inTightList
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    public function renderBlock(AbstractBlock $block, $inTightList = false)
    {
        return '';
    }

    /**
     * @param AbstractBlock[] $blocks
     * @param bool $inTightList
     *
     * @return string
     */
    public function renderBlocks($blocks, $inTightList = false)
    {
        return '';
    }
}
