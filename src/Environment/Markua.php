<?php
namespace League\CommonMark\Environment;

use League\CommonMark\Block\Parser as BlockParser;
use League\CommonMark\Block\Renderer as BlockRenderer;
use League\CommonMark\Markua\Block\Parser as MarkuaBlockParser;
use League\CommonMark\Markua\Block\Renderer as MarkuaBlockRenderer;

class Markua extends CommonMark {

    public function getBlockParsers()
    {
        return array(
            // This order is important
            new BlockParser\IndentedCodeParser(),
            new BlockParser\LazyParagraphParser(),
            new BlockParser\BlockQuoteParser(),
            new MarkuaBlockParser\AsideParser(),
            new BlockParser\ATXHeaderParser(),
            new BlockParser\FencedCodeParser(),
            new BlockParser\HtmlBlockParser(),
            new BlockParser\SetExtHeaderParser(),
            new BlockParser\HorizontalRuleParser(),
            new BlockParser\ListParser(),
        );
    }

    public function getBlockRenderers()
    {
        $renderers = parent::getBlockRenderers();
        $renderers['League\CommonMark\Markua\Block\Element\Aside'] = new MarkuaBlockRenderer\AsideRenderer();

        return $renderers;
    }
}
