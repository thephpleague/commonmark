---
layout: default
title: Upgrading from 1.5 to 2.0
description: Guide to upgrading to newer versions of this library
---

# Upgrading from 1.5 to 2.0

## Minimum PHP Version

The minimum supported PHP version was increased from 7.1 to 7.2.

## Method Return Types

Return types have been added to virtually all class and interface methods.  If you implement or extend anything from this library, ensure you also have the proper return types added.

## Classes/Namespaces Renamed

Many classes now live in different namespaces, and some have also been renamed.  Here's a quick guide showing their new locations:

_(Note that the base namespace of `League\CommonMark` has been omitted from this table for brevity.)_

| Old Class Namespace/Name (1.x)                                                        | New Class Namespace/Name (2.0)                                                           |
| ------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------- |
| `Util\ConfigurationAwareInterface`                                                    | `Configuration\ConfigurationAwareInterface`                                              |
| `Util\ConfigurationInterface`                                                         | `Configuration\ConfigurationInterface`                                                   |
| `Util\Configuration`                                                                  | `Configuration\Configuration`                                                            |
| `ConfigurableEnvironmentInterface`                                                    | `Environment\ConfigurableEnvironmentInterface`                                           |
| `EnvironmentAwareInterface`                                                           | `Environment\EnvironmentAwareInterface`                                                  |
| `Environment`                                                                         | `Environment\Environment`                                                                |
| `EnvironmentInterface`                                                                | `Environment\EnvironmentInterface`                                                       |
| `Extension\CommonMarkCoreExtension`                                                   | `Extension\CommonMark\CommonMarkCoreExtension`                                           |
| `Delimiter\Processor\EmphasisDelimiterProcessor`                                      | `Extension\CommonMark\Delimiter\Processor\EmphasisDelimiterProcessor`                    |
| `Block\Element\BlockQuote`                                                            | `Extension\CommonMark\Node\Block\BlockQuote`                                             |
| `Block\Element\FencedCode`                                                            | `Extension\CommonMark\Node\Block\FencedCode`                                             |
| `Block\Element\Heading`                                                               | `Extension\CommonMark\Node\Block\Heading`                                                |
| `Block\Element\HtmlBlock`                                                             | `Extension\CommonMark\Node\Block\HtmlBlock`                                              |
| `Block\Element\IndentedCode`                                                          | `Extension\CommonMark\Node\Block\IndentedCode`                                           |
| `Block\Element\ListBlock`                                                             | `Extension\CommonMark\Node\Block\ListBlock`                                              |
| `Block\Element\ListData`                                                              | `Extension\CommonMark\Node\Block\ListData`                                               |
| `Block\Element\ListItem`                                                              | `Extension\CommonMark\Node\Block\ListItem`                                               |
| `Block\Element\ThematicBreak`                                                         | `Extension\CommonMark\Node\Block\ThematicBreak`                                          |
| `Inline\Element\AbstractWebResource`                                                  | `Extension\CommonMark\Node\Inline\AbstractWebResource`                                   |
| `Inline\Element\Code`                                                                 | `Extension\CommonMark\Node\Inline\Code`                                                  |
| `Inline\Element\Emphasis`                                                             | `Extension\CommonMark\Node\Inline\Emphasis`                                              |
| `Inline\Element\HtmlInline`                                                           | `Extension\CommonMark\Node\Inline\HtmlInline`                                            |
| `Inline\Element\Image`                                                                | `Extension\CommonMark\Node\Inline\Image`                                                 |
| `Inline\Element\Link`                                                                 | `Extension\CommonMark\Node\Inline\Link`                                                  |
| `Inline\Element\Strong`                                                               | `Extension\CommonMark\Node\Inline\Strong`                                                |
| `Block\Parser\BlockQuoteParser`                                                       | `Extension\CommonMark\Parser\Block\BlockQuoteParser`                                     |
| `Block\Parser\FencedCodeParser`                                                       | `Extension\CommonMark\Parser\Block\FencedCodeParser`                                     |
| `Block\Parser\ATXHeadingParser` and `Block\Parser\SetExtHeadingParser`                | `Extension\CommonMark\Parser\Block\HeadingParser`                                        |
| `Block\Parser\HtmlBlockParser`                                                        | `Extension\CommonMark\Parser\Block\HtmlBlockParser`                                      |
| `Block\Parser\IndentedCodeParser`                                                     | `Extension\CommonMark\Parser\Block\IndentedCodeParser`                                   |
| `Block\Parser\ListParser`                                                             | `Extension\CommonMark\Parser\Block\ListParser`                                           |
| `Block\Parser\ThematicBreakParser`                                                    | `Extension\CommonMark\Parser\Block\ThematicBreakParser`                                  |
| `Inline\Parser\AutolinkParser`                                                        | `Extension\CommonMark\Parser\Inline\AutolinkParser`                                      |
| `Inline\Parser\BacktickParser`                                                        | `Extension\CommonMark\Parser\Inline\BacktickParser`                                      |
| `Inline\Parser\BangParser`                                                            | `Extension\CommonMark\Parser\Inline\BangParser`                                          |
| `Inline\Parser\CloseBracketParser`                                                    | `Extension\CommonMark\Parser\Inline\CloseBracketParser`                                  |
| `Inline\Parser\EntityParser`                                                          | `Extension\CommonMark\Parser\Inline\EntityParser`                                        |
| `Inline\Parser\EscapableParser`                                                       | `Extension\CommonMark\Parser\Inline\EscapableParser`                                     |
| `Inline\Parser\HtmlInlineParser`                                                      | `Extension\CommonMark\Parser\Inline\HtmlInlineParser`                                    |
| `Inline\Parser\OpenBracketParser`                                                     | `Extension\CommonMark\Parser\Inline\OpenBracketParser`                                   |
| `Block\Renderer\BlockQuoteRenderer`                                                   | `Extension\CommonMark\Renderer\Block\BlockQuoteRenderer`                                 |
| `Block\Renderer\FencedCodeRenderer`                                                   | `Extension\CommonMark\Renderer\Block\FencedCodeRenderer`                                 |
| `Block\Renderer\HeadingRenderer`                                                      | `Extension\CommonMark\Renderer\Block\HeadingRenderer`                                    |
| `Block\Renderer\HtmlBlockRenderer`                                                    | `Extension\CommonMark\Renderer\Block\HtmlBlockRenderer`                                  |
| `Block\Renderer\IndentedCodeRenderer`                                                 | `Extension\CommonMark\Renderer\Block\IndentedCodeRenderer`                               |
| `Block\Renderer\ListBlockRenderer`                                                    | `Extension\CommonMark\Renderer\Block\ListBlockRenderer`                                  |
| `Block\Renderer\ListItemRenderer`                                                     | `Extension\CommonMark\Renderer\Block\ListItemRenderer`                                   |
| `Block\Renderer\ThematicBreakRenderer`                                                | `Extension\CommonMark\Renderer\Block\ThematicBreakRenderer`                              |
| `Inline\Renderer\CodeRenderer`                                                        | `Extension\CommonMark\Renderer\Inline\CodeRenderer`                                      |
| `Inline\Renderer\EmphasisRenderer`                                                    | `Extension\CommonMark\Renderer\Inline\EmphasisRenderer`                                  |
| `Inline\Renderer\HtmlInlineRenderer`                                                  | `Extension\CommonMark\Renderer\Inline\HtmlInlineRenderer`                                |
| `Inline\Renderer\ImageRenderer`                                                       | `Extension\CommonMark\Renderer\Inline\ImageRenderer`                                     |
| `Inline\Renderer\LinkRenderer`                                                        | `Extension\CommonMark\Renderer\Inline\LinkRenderer`                                      |
| `Inline\Renderer\StrongRenderer`                                                      | `Extension\CommonMark\Renderer\Inline\StrongRenderer`                                    |
| `Extension\TableOfContents\TableOfContents`                                           | `Extension\TableOfContents\Node\TableOfContents`                                         |
| `Block\Element\AbstractBlock`                                                         | `Node\Block\AbstractBlock`                                                               |
| `Block\Element\Document`                                                              | `Node\Block\Document`                                                                    |
| `Block\Element\InlineContainerInterface`                                              | `Node\Block\InlineContainerInterface`                                                    |
| `Block\Element\Paragraph`                                                             | `Node\Block\Paragraph`                                                                   |
| `Block\Element\StringContainerInterface`                                              | `Node\StringContainerInterface`                                                          |
| `Inline\Element\AbstractInline`                                                       | `Node\Inline\AbstractInline`                                                             |
| `Inline\Element\AbstractStringContainer`                                              | `Node\Inline\AbstractStringContainer`                                                    |
| `Inline\AdjacentTextMerger`                                                           | `Node\Inline\AdjacentTextMerger`                                                         |
| `Inline\Element\Newline`                                                              | `Node\Inline\Newline`                                                                    |
| `Inline\Element\Text`                                                                 | `Node\Inline\Text`                                                                       |
| `Block\Parser\BlockParserInterface`                                                   | `Parser\Block\BlockContinueParserInterface` and `Parser\Block\BlockStartParserInterface` |
| `Block\Parser\LazyParagraphParser`                                                    | `Parser\Block\ParagraphParser`                                                           |
| `Cursor`                                                                              | `Parser\Cursor`                                                                          |
| `DocParser`                                                                           | `Parser\MarkdownParser`                                                                  |
| `DocParserInterface`                                                                  | `Parser\MarkdownParserInterface`                                                         |
| `Inline\Parser\InlineParserInterface`                                                 | `Parser\Inline\InlineParserInterface`                                                    |
| `Inline\Parser\NewlineParser`                                                         | `Parser\Inline\NewlineParser`                                                            |
| `InlineParserContext`                                                                 | `Parser\InlineParserContext`                                                             |
| `InlineParserEngine`                                                                  | `Parser\InlineParserEngine`                                                              |
| `Block\Renderer\DocumentRenderer`                                                     | `Renderer\Block\DocumentRenderer`                                                        |
| `Block\Renderer\ParagraphRenderer`                                                    | `Renderer\Block\ParagraphRenderer`                                                       |
| `HtmlRenderer`                                                                        | `Renderer\HtmlRenderer`                                                                  |
| `ElementRendererInterface`                                                            | `Renderer\HtmlRendererInterface`                                                         |
| `Inline\Renderer\NewlineRenderer`                                                     | `Renderer\Inline\NewlineRenderer`                                                        |
| `Inline\Renderer\TextRenderer`                                                        | `Renderer\Inline\TextRenderer`                                                           |
| `Block\Renderer\BlockRendererInterface` and `Inline\Renderer\InlineRendererInterface` | `Renderer\NodeRendererInterface`                                                         |
| `HtmlElement`                                                                         | `Util\HtmlElement`                                                                       |

## New Block Parsing Approach

We've completely changed how block parsing works in 2.0.  In a nutshell, 1.x had parsing responsibilities split between
the parser and the node. But nodes should be "dumb" and not know anything about how they are parsed - they should only
know the bare minimum needed for rendering.

As a result, 2.x has delegated the parsing responsibilities to two different interfaces:

|  Responsibility                                           | Old Method (1.x)                           | New Method (2.0)                                               |
| --------------------------------------------------------- | ------------------------------------------ | -------------------------------------------------------------- |
| Identifying the start of a block                          | `BlockParserInterface::parse()`            | `BlockStartParserInterface::tryStart()`                        |
| Instantiating and configuring the new block               | `BlockParserInterface::parse()`            | `BlockContinueParserInterface::__construct()`                  |
| Determining if the block acts as a container              | `AbstractBlock::isContainer()`             | `BlockContinueParserInterface::isContainer()`                  |
| Determining if the block can have lazy continuation lines | `AbstractBlock::isCode()`                  | `BlockContinueParserInterface::canHaveLazyContinuationLines()` |
| Determining if the block can contain certain child blocks | `AbstractBlock::canContain()`              | `BlockContinueParserInterface::canContain()`                   |
| Determining if the block continues on the next line       | `AbstractBlock::matchesNextLine()`         | `BlockContinueParserInterface::tryContinue()`                  |
| Adding the next line to the block                         | `AbstractBlock::handleRemainingContents()` | `BlockContinueParserInterface::addLine()`                      |
| Finalizing the block and its contents                     | `AbstractBlock::finalize()`                | `BlockContinueParserInterface::closeBlock()`                   |

As a result of making this change, the `addBlockParser()` method on `ConfigurableEnvironmentInterface` has changed to `addBlockStartParser()`.

See [the block parsing documentation](/2.0/customization/block-parsing/) for more information on this new approach.

## Rendering Changes

This library no longer differentiates between block renderers and inline renderers - everything now uses "node renderers" which allow us to have a unified approach to rendering!  As a result, the following changes were made, which you may need to change in your custom extensions:

| Old Method/Interface (1.x)                              | New Method/Interface (2.0)                        |
| ------------------------------------------------------- | ------------------------------------------------- |
| `BlockRendererInterface`                                | `NodeRendererInterface`                           |
| `InlineRendererInterface`                               | `NodeRendererInterface`                           |
| `EnvironmentInterface::getBlockRenderersForClass()`     | `EnvironmentInterface::getRenderersForClass()`    |
| `EnvironmentInterface::getInlineRenderersForClass()`    | `EnvironmentInterface::getRenderersForClass()`    |
| `ConfigurableEnvironmentInterface::addBlockRenderer()`  | `ConfigurableEnvironmentInterface::addRenderer()` |
| `ConfigurableEnvironmentInterface::addInlineRenderer()` | `ConfigurableEnvironmentInterface::addRenderer()` |
| `ElementRendererInterface::renderBlock()`               | `ChildNodeRendererInterface::renderNodes()`       |
| `ElementRendererInterface::renderBlocks()`              | `ChildNodeRendererInterface::renderNodes()`       |
| `ElementRendererInterface::renderInline()`              | `ChildNodeRendererInterface::renderNodes()`       |
| `ElementRendererInterface::renderInlines()`             | `ChildNodeRendererInterface::renderNodes()`       |
| `HtmlRenderer::renderBlock($document)`                  | `HtmlRenderer::renderDocument()`                  |

Renderers now implement the unified `NodeRendererInterface` which has a similar (but slightly different) signature from
the old `BlockRendererInterface` and `InlineRendererInterface` interfaces:

~~~php
/**
 * @param Node                       $node
 * @param ChildNodeRendererInterface $childRenderer
 *
 * @return HtmlElement|string|null
 */
public function render(Node $node, ChildNodeRendererInterface $childRenderer);
~~~

The element being rendered is still passed in the first argument, and the object that helps you render children is still
passed in the second argument.  Note that blocks are no longer told whether they're being rendered in a tight list - if you
need to know about this, traverse up the `$node` AST yourself and check any `ListBlock` ancestor for tightness.

## Removed Classes

The following classes have been removed:

| Class name in 1.x              | Replacement / Notes                                                                                           |
| ------------------------------ | ------------------------------------------------------------------------------------------------------------- |
| `AbstractStringContainerBlock` | Use `extends AbstractBlock implements StringContainerInterface` instead. Note the new method names.           |
| `Context`                      | Use `MarkdownParserState` instead (has different methods but serves a similar purpose)                        |
| `ContextInterface`             | Use `MarkdownParserStateInterface` instead (has different methods but serves a similar purpose)               |
| `Converter`                    | Use `CommonMarkConverter` instead. Note that this has a different constructor but the same methods.           |
| `ConverterInterface`           | Use `MarkdownConverterInterface`.  This interface has the same methods so it should be a drop-in replacement. |
| `UnmatchedBlockCloser`         | No longer needed 2.x                                                                                          |

## `EnvironmentInterface::HTML_INPUT_*` constants moved

The following constants have been moved:

| Old Location (1.x)                        | New Location (2.0)   |
| ----------------------------------------- | -------------------- |
| `EnvironmentInterface::HTML_INPUT_ALLOW`  | `HtmlFilter::ALLOW`  |
| `EnvironmentInterface::HTML_INPUT_ESCAPE` | `HtmlFilter::ESCAPE` |
| `EnvironmentInterface::HTML_INPUT_STRIP`  | `HtmlFilter::STRIP`  |

## Renamed Methods

The following methods have been renamed:

| Class                                              | Old Name (1.x)     | New Name (2.0)          |
| -------------------------------------------------- | ------------------ | ----------------------- |
| `Environment` / `ConfigurableEnvironmentInterface` | `addBlockParser()` | `addBlockStartParser()` |
| `ReferenceMap` / `ReferenceMapInterface`           | `addReference()`   | `add()`                 |
| `ReferenceMap` / `ReferenceMapInterface`           | `getReference()`   | `get()`                 |
| `ReferenceMap` / `ReferenceMapInterface`           | `listReferences()` | `getIterator()`         |

## Visibility Changes

The following properties have had their visibility changed:

| Property               | Was (1.x) | Is Now (2.0) | Notes                                     |
| ---------------------- | --------- | ------------ | ----------------------------------------- |
| `TableCell::$align`    | `public`  | `private`    | Use `getAlign()` and `setAlign()` instead |
| `TableCell::$type`     | `public`  | `private`    | Use `getType()` and `setType()` instead   |
| `TableSection::$type`  | `public`  | `private`    | Use `getType()` instead                   |

## New approach to the `ReferenceParser`

The `ReferenceParser` class in 1.x worked on complete paragraphs of text.  This has been changed in 2.x to work in a more-gradual fashion, where parsing is done on-the-fly as new lines are added.
Whereas you may have previously called `parse()` on a `Cursor` once on something containing multiple lines, you should now call `parse()` on each line of text and then later call `getReferences()`
to check what has been parsed.

## `Html5Entities` utility class removed

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

## Unused methods

The following unused methods have been removed:

 - `Delimiter::setCanClose()`
