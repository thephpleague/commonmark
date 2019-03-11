<?php

/*
 * This file is part of the league/commonmark-ext-inlines-only package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Ext\InlinesOnly;

use League\CommonMark\Block\Element\Document;
use League\CommonMark\Block\Element\Paragraph;
use League\CommonMark\Block\Parser as BlockParser;
use League\CommonMark\Extension\Extension;
use League\CommonMark\Inline\Element as InlineElement;
use League\CommonMark\Inline\Parser as InlineParser;
use League\CommonMark\Inline\Processor as InlineProcessor;
use League\CommonMark\Inline\Renderer as InlineRenderer;

final class InlinesOnlyExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function getBlockParsers()
    {
        return [
            new BlockParser\LazyParagraphParser(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getInlineParsers()
    {
        return [
            new InlineParser\NewlineParser(),
            new InlineParser\BacktickParser(),
            new InlineParser\EscapableParser(),
            new InlineParser\EntityParser(),
            new InlineParser\EmphasisParser(),
            new InlineParser\AutolinkParser(),
            new InlineParser\HtmlInlineParser(),
            new InlineParser\CloseBracketParser(),
            new InlineParser\OpenBracketParser(),
            new InlineParser\BangParser(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getInlineProcessors()
    {
        return [
            new InlineProcessor\EmphasisProcessor(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockRenderers()
    {
        $renderer = new ChildRenderer();

        return [
            Document::class  => $renderer,
            Paragraph::class => $renderer,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getInlineRenderers()
    {
        return [
            InlineElement\Code::class       => new InlineRenderer\CodeRenderer(),
            InlineElement\Emphasis::class   => new InlineRenderer\EmphasisRenderer(),
            InlineElement\HtmlInline::class => new InlineRenderer\HtmlInlineRenderer(),
            InlineElement\Image::class      => new InlineRenderer\ImageRenderer(),
            InlineElement\Link::class       => new InlineRenderer\LinkRenderer(),
            InlineElement\Newline::class    => new InlineRenderer\NewlineRenderer(),
            InlineElement\Strong::class     => new InlineRenderer\StrongRenderer(),
            InlineElement\Text::class       => new InlineRenderer\TextRenderer(),
        ];
    }
}
