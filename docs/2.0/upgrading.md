---
layout: default
title: Upgrading from 1.4 to 2.0
description: Guide to upgrading to newer versions of this library
---

# Upgrading from 1.4 to 2.0

## Minimum PHP Version

The minimum supported PHP version was increased from 7.1 to 7.2.

## Classes/Namespaces Renamed

Many classes now live in different namespaces.  Here's a quick guide showing their new locations:

_(Note that the base namespace of `League\CommonMark` has been omitted from this table for brevity.)_

| New Class Name | Old Class Name |
| -------------- | -------------- |
| `Configuration\ConfigurationAwareInterface` | `Util\ConfigurationAwareInterface` |
| `Configuration\ConfigurationInterface` | `Util\ConfigurationInterface` |
| `Configuration\Configuration` | `Util\Configuration` |
| `Environment\ConfigurableEnvironmentInterface` | `ConfigurableEnvironmentInterface` |
| `Environment\EnvironmentAwareInterface` | `EnvironmentAwareInterface` |
| `Environment\Environment` | `Environment` |
| `Environment\EnvironmentInterface` | `EnvironmentInterface` |
| `Extension\CommonMark\CommonMarkCoreExtension` | `Extension\CommonMarkCoreExtension` |
| `Extension\CommonMark\Delimiter\Processor\EmphasisDelimiterProcessor` | `Delimiter\Processor\EmphasisDelimiterProcessor` |
| `Extension\CommonMark\Node\Block\BlockQuote` | `Block\Element\BlockQuote` |
| `Extension\CommonMark\Node\Block\FencedCode` | `Block\Element\FencedCode` |
| `Extension\CommonMark\Node\Block\Heading` | `Block\Element\Heading` |
| `Extension\CommonMark\Node\Block\HtmlBlock` | `Block\Element\HtmlBlock` |
| `Extension\CommonMark\Node\Block\IndentedCode` | `Block\Element\IndentedCode` |
| `Extension\CommonMark\Node\Block\ListBlock` | `Block\Element\ListBlock` |
| `Extension\CommonMark\Node\Block\ListData` | `Block\Element\ListData` |
| `Extension\CommonMark\Node\Block\ListItem` | `Block\Element\ListItem` |
| `Extension\CommonMark\Node\Block\ThematicBreak` | `Block\Element\ThematicBreak` |
| `Extension\CommonMark\Node\Inline\AbstractWebResource` | `Inline\Element\AbstractWebResource` |
| `Extension\CommonMark\Node\Inline\Code` | `Inline\Element\Code` |
| `Extension\CommonMark\Node\Inline\Emphasis` | `Inline\Element\Emphasis` |
| `Extension\CommonMark\Node\Inline\HtmlInline` | `Inline\Element\HtmlInline` |
| `Extension\CommonMark\Node\Inline\Image` | `Inline\Element\Image` |
| `Extension\CommonMark\Node\Inline\Link` | `Inline\Element\Link` |
| `Extension\CommonMark\Node\Inline\Strong` | `Inline\Element\Strong` |
| `Extension\CommonMark\Parser\Block\ATXHeadingParser` | `Block\Parser\ATXHeadingParser` |
| `Extension\CommonMark\Parser\Block\BlockQuoteParser` | `Block\Parser\BlockQuoteParser` |
| `Extension\CommonMark\Parser\Block\FencedCodeParser` | `Block\Parser\FencedCodeParser` |
| `Extension\CommonMark\Parser\Block\HtmlBlockParser` | `Block\Parser\HtmlBlockParser` |
| `Extension\CommonMark\Parser\Block\IndentedCodeParser` | `Block\Parser\IndentedCodeParser` |
| `Extension\CommonMark\Parser\Block\ListParser` | `Block\Parser\ListParser` |
| `Extension\CommonMark\Parser\Block\SetExtHeadingParser` | `Block\Parser\SetExtHeadingParser` |
| `Extension\CommonMark\Parser\Block\ThematicBreakParser` | `Block\Parser\ThematicBreakParser` |
| `Extension\CommonMark\Parser\Inline\AutolinkParser` | `Inline\Parser\AutolinkParser` |
| `Extension\CommonMark\Parser\Inline\BacktickParser` | `Inline\Parser\BacktickParser` |
| `Extension\CommonMark\Parser\Inline\BangParser` | `Inline\Parser\BangParser` |
| `Extension\CommonMark\Parser\Inline\CloseBracketParser` | `Inline\Parser\CloseBracketParser` |
| `Extension\CommonMark\Parser\Inline\EntityParser` | `Inline\Parser\EntityParser` |
| `Extension\CommonMark\Parser\Inline\EscapableParser` | `Inline\Parser\EscapableParser` |
| `Extension\CommonMark\Parser\Inline\HtmlInlineParser` | `Inline\Parser\HtmlInlineParser` |
| `Extension\CommonMark\Parser\Inline\OpenBracketParser` | `Inline\Parser\OpenBracketParser` |
| `Extension\CommonMark\Renderer\Block\BlockQuoteRenderer` | `Block\Renderer\BlockQuoteRenderer` |
| `Extension\CommonMark\Renderer\Block\FencedCodeRenderer` | `Block\Renderer\FencedCodeRenderer` |
| `Extension\CommonMark\Renderer\Block\HeadingRenderer` | `Block\Renderer\HeadingRenderer` |
| `Extension\CommonMark\Renderer\Block\HtmlBlockRenderer` | `Block\Renderer\HtmlBlockRenderer` |
| `Extension\CommonMark\Renderer\Block\IndentedCodeRenderer` | `Block\Renderer\IndentedCodeRenderer` |
| `Extension\CommonMark\Renderer\Block\ListBlockRenderer` | `Block\Renderer\ListBlockRenderer` |
| `Extension\CommonMark\Renderer\Block\ListItemRenderer` | `Block\Renderer\ListItemRenderer` |
| `Extension\CommonMark\Renderer\Block\ThematicBreakRenderer` | `Block\Renderer\ThematicBreakRenderer` |
| `Extension\CommonMark\Renderer\Inline\CodeRenderer` | `Inline\Renderer\CodeRenderer` |
| `Extension\CommonMark\Renderer\Inline\EmphasisRenderer` | `Inline\Renderer\EmphasisRenderer` |
| `Extension\CommonMark\Renderer\Inline\HtmlInlineRenderer` | `Inline\Renderer\HtmlInlineRenderer` |
| `Extension\CommonMark\Renderer\Inline\ImageRenderer` | `Inline\Renderer\ImageRenderer` |
| `Extension\CommonMark\Renderer\Inline\LinkRenderer` | `Inline\Renderer\LinkRenderer` |
| `Extension\CommonMark\Renderer\Inline\StrongRenderer` | `Inline\Renderer\StrongRenderer` |
| `Node\Block\AbstractBlock` | `Block\Element\AbstractBlock` |
| `Node\Block\AbstractStringContainerBlock` | `Block\Element\AbstractStringContainerBlock` |
| `Node\Block\Document` | `Block\Element\Document` |
| `Node\Block\InlineContainerInterface` | `Block\Element\InlineContainerInterface` |
| `Node\Block\Paragraph` | `Block\Element\Paragraph` |
| `Node\Block\StringContainerInterface` | `Block\Element\StringContainerInterface` |
| `Node\Inline\AbstractInline` | `Inline\Element\AbstractInline` |
| `Node\Inline\AbstractStringContainer` | `Inline\Element\AbstractStringContainer` |
| `Node\Inline\AdjacentTextMerger` | `Inline\AdjacentTextMerger` |
| `Node\Inline\Newline` | `Inline\Element\Newline` |
| `Node\Inline\Text` | `Inline\Element\Text` |
| `Parser\Block\BlockParserInterface` | `Block\Parser\BlockParserInterface` |
| `Parser\Block\LazyParagraphParser` | `Block\Parser\LazyParagraphParser` |
| `Parser\Context` | `Context` |
| `Parser\ContextInterface` | `ContextInterface` |
| `Parser\Cursor` | `Cursor` |
| `Parser\DocParser` | `DocParser` |
| `Parser\DocParserInterface` | `DocParserInterface` |
| `Parser\Inline\InlineParserInterface` | `Inline\Parser\InlineParserInterface` |
| `Parser\Inline\NewlineParser` | `Inline\Parser\NewlineParser` |
| `Parser\InlineParserContext` | `InlineParserContext` |
| `Parser\InlineParserEngine` | `InlineParserEngine` |
| `Parser\UnmatchedBlockCloser` | `UnmatchedBlockCloser` |
| `Renderer\Block\BlockRendererInterface` | `Block\Renderer\BlockRendererInterface` |
| `Renderer\Block\DocumentRenderer` | `Block\Renderer\DocumentRenderer` |
| `Renderer\Block\ParagraphRenderer` | `Block\Renderer\ParagraphRenderer` |
| `Renderer\ElementRendererInterface` | `ElementRendererInterface` |
| `Renderer\HtmlRenderer` | `HtmlRenderer` |
| `Renderer\Inline\InlineRendererInterface` | `Inline\Renderer\InlineRendererInterface` |
| `Renderer\Inline\NewlineRenderer` | `Inline\Renderer\NewlineRenderer` |
| `Renderer\Inline\TextRenderer` | `Inline\Renderer\TextRenderer` |
| `Util\HtmlElement` | `HtmlElement` |

## `Converter` class and `ConverterInterface` removed

Any usages of `Converter` should be replaced with `CommonMarkConverter`. Note that this has a different constructor but the same methods.

Any usages of `ConverterInterface` should be replaced with `MarkdownConverterInterface`.  This interface has the same methods, so it should be a drop-in replacement.

## `Html5Entities` utility class

Use the `Html5EntityDecoder` utility class instead.

## `bin/commonmark` command

This command was buggy to test and was relatively unpopular, so it has been removed. If you need this type of functionality, consider writing your own script with a Converter/Environment configured exactly how you want it.

## `ArrayCollection` methods

Several methods were removed from this class - here are the methods along with possible alternatives you can switch to:

| Removed Method Name | Alternative                                          |
| ------------------- | ---------------------------------------------------- |
| `add($value)`       | `$collection[] = $value`                             |
| `set($key, $value)` | `$collection[$key] = $value`                         |
| `get($key)`         | `$collection[$key]`                                  |
| `remove($key)`      | `unset($collection[$key])`                           |
| `isEmpty()`         | `count($collection) === 0`                           |
| `contains($value)`  | `in_array($value, $collection->toArray(), true)`     |
| `indexOf($value)`   | `array_search($value, $collection->toArray(), true)` |
| `containsKey($key)` | `isset($collection[$key])`                           |
| `replaceWith()`     | (none provided)                                      |
| `removeGaps()`      | (none provided)                                      |
