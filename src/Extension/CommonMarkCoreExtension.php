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

namespace League\CommonMark\Extension;

use League\CommonMark\Block\Parser as BlockParser;
use League\CommonMark\Block\Renderer as BlockRenderer;
use League\CommonMark\ConfigurableEnvironmentInterface;
use League\CommonMark\Inline\Parser as InlineParser;
use League\CommonMark\Inline\Processor as InlineProcessor;
use League\CommonMark\Inline\Renderer as InlineRenderer;

final class CommonMarkCoreExtension implements ExtensionInterface
{
    public function register(ConfigurableEnvironmentInterface $environment)
    {
        $environment
            // This order is important
            ->addBlockParser(new BlockParser\BlockQuoteParser())
            ->addBlockParser(new BlockParser\ATXHeadingParser())
            ->addBlockParser(new BlockParser\FencedCodeParser())
            ->addBlockParser(new BlockParser\HtmlBlockParser())
            ->addBlockParser(new BlockParser\SetExtHeadingParser())
            ->addBlockParser(new BlockParser\ThematicBreakParser())
            ->addBlockParser(new BlockParser\ListParser())
            ->addBlockParser(new BlockParser\IndentedCodeParser())
            ->addBlockParser(new BlockParser\LazyParagraphParser())

            ->addInlineParser(new InlineParser\NewlineParser())
            ->addInlineParser(new InlineParser\BacktickParser())
            ->addInlineParser(new InlineParser\EscapableParser())
            ->addInlineParser(new InlineParser\EntityParser())
            ->addInlineParser(new InlineParser\EmphasisParser())
            ->addInlineParser(new InlineParser\AutolinkParser())
            ->addInlineParser(new InlineParser\HtmlInlineParser())
            ->addInlineParser(new InlineParser\CloseBracketParser())
            ->addInlineParser(new InlineParser\OpenBracketParser())
            ->addInlineParser(new InlineParser\BangParser())

            ->addInlineProcessor(new InlineProcessor\EmphasisProcessor())

            ->addBlockRenderer('League\CommonMark\Block\Element\BlockQuote',    new BlockRenderer\BlockQuoteRenderer())
            ->addBlockRenderer('League\CommonMark\Block\Element\Document',      new BlockRenderer\DocumentRenderer())
            ->addBlockRenderer('League\CommonMark\Block\Element\FencedCode',    new BlockRenderer\FencedCodeRenderer())
            ->addBlockRenderer('League\CommonMark\Block\Element\Heading',       new BlockRenderer\HeadingRenderer())
            ->addBlockRenderer('League\CommonMark\Block\Element\HtmlBlock',     new BlockRenderer\HtmlBlockRenderer())
            ->addBlockRenderer('League\CommonMark\Block\Element\IndentedCode',  new BlockRenderer\IndentedCodeRenderer())
            ->addBlockRenderer('League\CommonMark\Block\Element\ListBlock',     new BlockRenderer\ListBlockRenderer())
            ->addBlockRenderer('League\CommonMark\Block\Element\ListItem',      new BlockRenderer\ListItemRenderer())
            ->addBlockRenderer('League\CommonMark\Block\Element\Paragraph',     new BlockRenderer\ParagraphRenderer())
            ->addBlockRenderer('League\CommonMark\Block\Element\ThematicBreak', new BlockRenderer\ThematicBreakRenderer())

            ->addInlineRenderer('League\CommonMark\Inline\Element\Code',       new InlineRenderer\CodeRenderer())
            ->addInlineRenderer('League\CommonMark\Inline\Element\Emphasis',   new InlineRenderer\EmphasisRenderer())
            ->addInlineRenderer('League\CommonMark\Inline\Element\HtmlInline', new InlineRenderer\HtmlInlineRenderer())
            ->addInlineRenderer('League\CommonMark\Inline\Element\Image',      new InlineRenderer\ImageRenderer())
            ->addInlineRenderer('League\CommonMark\Inline\Element\Link',       new InlineRenderer\LinkRenderer())
            ->addInlineRenderer('League\CommonMark\Inline\Element\Newline',    new InlineRenderer\NewlineRenderer())
            ->addInlineRenderer('League\CommonMark\Inline\Element\Strong',     new InlineRenderer\StrongRenderer())
            ->addInlineRenderer('League\CommonMark\Inline\Element\Text',       new InlineRenderer\TextRenderer())
        ;
    }
}
