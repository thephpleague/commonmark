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

namespace League\CommonMark;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;

/**
 * Renders a parsed AST to HTML
 */
class HtmlRenderer implements ElementRendererInterface
{
    /**
     * @var EnvironmentInterface
     */
    protected $environment;

    /**
     * @param EnvironmentInterface $environment
     */
    public function __construct(EnvironmentInterface $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @param string $option
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getOption($option, $default = null)
    {
        return $this->environment->getConfig('renderer/' . $option, $default);
    }

    /**
     * @param AbstractInline $inline
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    protected function renderInline(AbstractInline $inline)
    {
        $renderers = $this->environment->getInlineRenderersForClass(get_class($inline));

        /** @var InlineRendererInterface $renderer */
        foreach ($renderers as $renderer) {
            if (($result = $renderer->render($inline, $this)) !== null) {
                return $result;
            }
        }

        throw new \RuntimeException('Unable to find corresponding renderer for inline type ' . get_class($inline));
    }

    /**
     * @param AbstractInline[] $inlines
     *
     * @return string
     */
    public function renderInlines($inlines)
    {
        $result = [];
        foreach ($inlines as $inline) {
            $result[] = $this->renderInline($inline);
        }

        return implode('', $result);
    }

    /**
     * @param AbstractBlock $block
     * @param bool          $inTightList
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function renderBlock(AbstractBlock $block, $inTightList = false)
    {
        $renderers = $this->environment->getBlockRenderersForClass(get_class($block));

        /** @var BlockRendererInterface $renderer */
        foreach ($renderers as $renderer) {
            if (($result = $renderer->render($block, $this, $inTightList)) !== null) {
                return $result;
            }
        }

        throw new \RuntimeException('Unable to find corresponding renderer for block type ' . get_class($block));
    }

    /**
     * @param AbstractBlock[] $blocks
     * @param bool            $inTightList
     *
     * @return string
     */
    public function renderBlocks($blocks, $inTightList = false)
    {
        $result = [];
        foreach ($blocks as $block) {
            $result[] = $this->renderBlock($block, $inTightList);
        }

        $separator = $this->getOption('block_separator', "\n");

        return implode($separator, $result);
    }
}
