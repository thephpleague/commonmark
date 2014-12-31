<?php
namespace League\CommonMark\Environment;

use League\CommonMark\Block\Parser as BlockParser;
use League\CommonMark\Block\Renderer as BlockRenderer;
use League\CommonMark\EnvironmentInterface;
use League\CommonMark\Inline\Parser as InlineParser;
use League\CommonMark\Inline\Processor\EmphasisProcessor;
use League\CommonMark\Inline\Renderer as InlineRenderer;

class CommonMark implements EnvironmentInterface {

    public function getBlockParsers()
    {
        return array(
            // This order is important
            new BlockParser\IndentedCodeParser(),
            new BlockParser\LazyParagraphParser(),
            new BlockParser\BlockQuoteParser(),
            new BlockParser\ATXHeaderParser(),
            new BlockParser\FencedCodeParser(),
            new BlockParser\HtmlBlockParser(),
            new BlockParser\SetExtHeaderParser(),
            new BlockParser\HorizontalRuleParser(),
            new BlockParser\ListParser(),
        );
    }

    public function getInlineParsers()
    {
        return array(
            new InlineParser\NewlineParser(),
            new InlineParser\BacktickParser(),
            new InlineParser\EscapableParser(),
            new InlineParser\EntityParser(),
            new InlineParser\EmphasisParser(),
            new InlineParser\AutolinkParser(),
            new InlineParser\RawHtmlParser(),
            new InlineParser\CloseBracketParser(),
            new InlineParser\OpenBracketParser(),
            new InlineParser\BangParser(),
        );
    }

    public function getInlineProcessors()
    {
        return array(
            new EmphasisProcessor()
        );
    }

    public function getBlockRenderers()
    {
        return array(
            'League\CommonMark\Block\Element\BlockQuote'          => new BlockRenderer\BlockQuoteRenderer(),
            'League\CommonMark\Block\Element\Document'            => new BlockRenderer\DocumentRenderer(),
            'League\CommonMark\Block\Element\FencedCode'          => new BlockRenderer\FencedCodeRenderer(),
            'League\CommonMark\Block\Element\Header'              => new BlockRenderer\HeaderRenderer(),
            'League\CommonMark\Block\Element\HorizontalRule'      => new BlockRenderer\HorizontalRuleRenderer(),
            'League\CommonMark\Block\Element\HtmlBlock'           => new BlockRenderer\HtmlBlockRenderer(),
            'League\CommonMark\Block\Element\IndentedCode'        => new BlockRenderer\IndentedCodeRenderer(),
            'League\CommonMark\Block\Element\ListBlock'           => new BlockRenderer\ListBlockRenderer(),
            'League\CommonMark\Block\Element\ListItem'            => new BlockRenderer\ListItemRenderer(),
            'League\CommonMark\Block\Element\Paragraph'           => new BlockRenderer\ParagraphRenderer(),
            'League\CommonMark\Block\Element\ReferenceDefinition' => new BlockRenderer\ReferenceDefinitionRenderer(),
        );
    }

    public function getInlineRenderers()
    {
        return array(
            'League\CommonMark\Inline\Element\Code'     => new InlineRenderer\CodeRenderer(),
            'League\CommonMark\Inline\Element\Emphasis' => new InlineRenderer\EmphasisRenderer(),
            'League\CommonMark\Inline\Element\Html'     => new InlineRenderer\RawHtmlRenderer(),
            'League\CommonMark\Inline\Element\Image'    => new InlineRenderer\ImageRenderer(),
            'League\CommonMark\Inline\Element\Link'     => new InlineRenderer\LinkRenderer(),
            'League\CommonMark\Inline\Element\Newline'  => new InlineRenderer\NewlineRenderer(),
            'League\CommonMark\Inline\Element\Strong'   => new InlineRenderer\StrongRenderer(),
            'League\CommonMark\Inline\Element\Text'     => new InlineRenderer\TextRenderer(),
        );
    }
}
