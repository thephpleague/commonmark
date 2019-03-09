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
            ->addBlockParser(new BlockParser\BlockQuoteParser(),      70)
            ->addBlockParser(new BlockParser\ATXHeadingParser(),      60)
            ->addBlockParser(new BlockParser\FencedCodeParser(),      50)
            ->addBlockParser(new BlockParser\HtmlBlockParser(),       40)
            ->addBlockParser(new BlockParser\SetExtHeadingParser(),   30)
            ->addBlockParser(new BlockParser\ThematicBreakParser(),   20)
            ->addBlockParser(new BlockParser\ListParser(),            10)
            ->addBlockParser(new BlockParser\IndentedCodeParser(),  -100)
            ->addBlockParser(new BlockParser\LazyParagraphParser(), -200)

            ->addInlineParser(new InlineParser\NewlineParser(),     200)
            ->addInlineParser(new InlineParser\BacktickParser(),    150)
            ->addInlineParser(new InlineParser\EscapableParser(),    80)
            ->addInlineParser(new InlineParser\EntityParser(),       70)
            ->addInlineParser(new InlineParser\EmphasisParser(),     60)
            ->addInlineParser(new InlineParser\AutolinkParser(),     50)
            ->addInlineParser(new InlineParser\HtmlInlineParser(),   40)
            ->addInlineParser(new InlineParser\CloseBracketParser(), 30)
            ->addInlineParser(new InlineParser\OpenBracketParser(),  20)
            ->addInlineParser(new InlineParser\BangParser(),         10)

            ->addInlineProcessor(new InlineProcessor\EmphasisProcessor(), 0)

            ->addBlockRenderer('League\CommonMark\Block\Element\BlockQuote',    new BlockRenderer\BlockQuoteRenderer(),    0)
            ->addBlockRenderer('League\CommonMark\Block\Element\Document',      new BlockRenderer\DocumentRenderer(),      0)
            ->addBlockRenderer('League\CommonMark\Block\Element\FencedCode',    new BlockRenderer\FencedCodeRenderer(),    0)
            ->addBlockRenderer('League\CommonMark\Block\Element\Heading',       new BlockRenderer\HeadingRenderer(),       0)
            ->addBlockRenderer('League\CommonMark\Block\Element\HtmlBlock',     new BlockRenderer\HtmlBlockRenderer(),     0)
            ->addBlockRenderer('League\CommonMark\Block\Element\IndentedCode',  new BlockRenderer\IndentedCodeRenderer(),  0)
            ->addBlockRenderer('League\CommonMark\Block\Element\ListBlock',     new BlockRenderer\ListBlockRenderer(),     0)
            ->addBlockRenderer('League\CommonMark\Block\Element\ListItem',      new BlockRenderer\ListItemRenderer(),      0)
            ->addBlockRenderer('League\CommonMark\Block\Element\Paragraph',     new BlockRenderer\ParagraphRenderer(),     0)
            ->addBlockRenderer('League\CommonMark\Block\Element\ThematicBreak', new BlockRenderer\ThematicBreakRenderer(), 0)

            ->addInlineRenderer('League\CommonMark\Inline\Element\Code',       new InlineRenderer\CodeRenderer(),       0)
            ->addInlineRenderer('League\CommonMark\Inline\Element\Emphasis',   new InlineRenderer\EmphasisRenderer(),   0)
            ->addInlineRenderer('League\CommonMark\Inline\Element\HtmlInline', new InlineRenderer\HtmlInlineRenderer(), 0)
            ->addInlineRenderer('League\CommonMark\Inline\Element\Image',      new InlineRenderer\ImageRenderer(),      0)
            ->addInlineRenderer('League\CommonMark\Inline\Element\Link',       new InlineRenderer\LinkRenderer(),       0)
            ->addInlineRenderer('League\CommonMark\Inline\Element\Newline',    new InlineRenderer\NewlineRenderer(),    0)
            ->addInlineRenderer('League\CommonMark\Inline\Element\Strong',     new InlineRenderer\StrongRenderer(),     0)
            ->addInlineRenderer('League\CommonMark\Inline\Element\Text',       new InlineRenderer\TextRenderer(),       0)
        ;
    }
}
