# Change Log
All notable changes to this project will be documented in this file.
Updates should follow the [Keep a CHANGELOG](https://keepachangelog.com/) principles.

## [Unreleased][unreleased]

See <https://commonmark.thephpleague.com/2.0/upgrading/> for detailed information on upgrading to version 2.0.

### Added

 - Added new `HtmlFilter` and `StringContainerHelper` utility classes
 - Added new `AbstractBlockContinueParser` class to simplify the creation of custom block parsers
 - Added several new classes and interfaces:
   - `BlockContinue`
   - `BlockContinueParserInterface`
   - `BlockStart`
   - `BlockStartParserInterface`
   - `ChildNodeRendererInterface`
   - `CursorState`
   - `DocumentBlockParser`
   - `HtmlRendererInterface`
   - `InlineParserEngineInterface`
   - `MarkdownParserState`
   - `MarkdownParserStateInterface`
 - Added several new methods:
   - `FencedCode::setInfo()`
   - `Heading::setLevel()`
   - `HtmlRenderer::renderDocument()`
   - `InvalidOptionException::forConfigOption()`
   - `InvalidOptionException::forParameter()`
   - `LinkParserHelper::parsePartialLinkLabel()`
   - `LinkParserHelper::parsePartialLinkTitle()`
   - `RegexHelper::isLetter()`
   - `StringContainerInterface::setLiteral()`
   - `TableCell::getType()`
   - `TableCell::setType()`
   - `TableCell::getAlign()`
   - `TableCell::setAlign()`
 - Added purity markers throughout the codebase (verified with Psalm)

### Changed

 - Moved and renamed several classes - [see the full list here](https://commonmark.thephpleague.com/2.0/upgrading/#classesnamespaces-renamed)
 - Implemented a new approach to block parsing. This was a massive change, so here are the highlights:
   - Functionality previously found in block parsers and node elements has moved to block parser factories and block parsers, respectively ([more details](https://commonmark.thephpleague.com/2.0/upgrading/#new-block-parsing-approach))
   - `ConfigurableEnvironmentInterface::addBlockParser()` is now `ConfigurableEnvironmentInterface::addBlockParserFactory()`
   - `ReferenceParser` was re-implemented and works completely different than before
   - The paragraph parser no longer needs to be added manually to the environment
 - Changed block and inline rendering to use common methods and interfaces
   - `BlockRendererInterface` and `InlineRendererInterface` were replaced by `NodeRendererInterface` with slightly different parameters. All core renderers now implement this interface.
   - `ConfigurableEnvironmentInterface::addBlockRenderer()` and `addInlineRenderer()` are now just `addRenderer()`
   - `EnvironmentInterface::getBlockRenderersForClass()` and `getInlineRenderersForClass()` are now just `getRenderersForClass()`
 - Combined separate classes/interfaces into one:
   - `DisallowedRawHtmlRenderer` replaces `DisallowedRawHtmlBlockRenderer` and `DisallowedRawHtmlInlineRenderer`
   - `NodeRendererInterface` replaces `BlockRendererInterface` and `InlineRendererInterface`
 - Renamed the following methods:
   - `Environment` and `ConfigurableEnvironmentInterface`:
     - `addBlockParser()` is now `addBlockStartParser()`
   - `ReferenceMap` and `ReferenceMapInterface`:
     - `addReference()` is now `add()`
     - `getReference()` is now `get()`
     - `listReferences()` is now `getIterator()`
   - Various node (block/inline) classes:
     - `getContent()` is now `getLiteral()`
     - `setContent()` is now `setLiteral()`
 - Moved and renamed the following constants:
   - `EnvironmentInterface::HTML_INPUT_ALLOW` is now `HtmlFilter::ALLOW`
   - `EnvironmentInterface::HTML_INPUT_ESCAPE` is now `HtmlFilter::ESCAPE`
   - `EnvironmentInterface::HTML_INPUT_STRIP` is now `HtmlFilter::STRIP`
 - Changed the visibility of the following properties:
   - `TableCell::$align` is now `private`
   - `TableCell::$type` is now `private`
   - `TableSection::$type` is now `private`
 - Added missing return types to virtually every class and interface method
 - Several methods which previously returned `$this` now return `void`
   - `Delimiter::setPrevious()`
   - `Node::replaceChildren()`
   - `Context::setTip()`
   - `Context::setContainer()`
   - `Context::setBlocksParsed()`
   - `AbstractStringContainer::setContent()`
   - `AbstractWebResource::setUrl()`
 - `Heading` nodes no longer directly contain a copy of their inner text
 - `StringContainerInterface` can now be used for inlines, not just blocks
 - `ArrayCollection` is now final and only supports integer keys
 - `Cursor::saveState()` and `Cursor::restoreState()` now use `CursorState` objects instead of arrays
 - `NodeWalker::next()` now enters, traverses any children, and leaves all elements which may have children (basically all blocks plus any inlines with children). Previously, it only did this for elements explicitly marked as "containers".
 - `InvalidOptionException` now extends from `UnexpectedValueException`

### Removed

 - Removed support for PHP 7.1
 - Removed all previously-deprecated functionality:
   - Removed the `Converter` class and `ConverterInterface`
   - Removed the `bin/commonmark` script
   - Removed the `Html5Entities` utility class
   - Removed the `InlineMentionParser` (use `MentionParser` instead)
   - Removed `DefaultSlugGenerator` and `SlugGeneratorInterface` from the `Extension/HeadingPermalink/Slug` sub-namespace (use the new ones under `./SlugGenerator` instead)
   - Removed the following `ArrayCollection` methods:
     - `add()`
     - `set()`
     - `get()`
     - `remove()`
     - `isEmpty()`
     - `contains()`
     - `indexOf()`
     - `containsKey()`
     - `replaceWith()`
     - `removeGaps()`
   - Removed the `ListBlock::TYPE_UNORDERED` constant
   - Removed the `CommonMarkConverter::VERSION` constant
   - Removed the `HeadingPermalinkRenderer::DEFAULT_INNER_CONTENTS` constant
   - Removed the `heading_permalink/inner_contents` configuration option
 - Removed now-unused classes:
   - `AbstractStringContainerBlock`
   - `BlockRendererInterface`
   - `Context`
   - `ContextInterface`
   - `Converter`
   - `ConverterInterface`
   - `InlineRendererInterface`
   - `UnmatchedBlockCloser`
 - Removed the following methods, properties, and constants:
   - `AbstractBlock::$open`
   - `AbstractBlock::$lastLineBlank`
   - `AbstractBlock::isContainer()`
   - `AbstractBlock::canContain()`
   - `AbstractBlock::isCode()`
   - `AbstractBlock::matchesNextLine()`
   - `AbstractBlock::endsWithBlankLine()`
   - `AbstractBlock::setLastLineBlank()`
   - `AbstractBlock::shouldLastLineBeBlank()`
   - `AbstractBlock::isOpen()`
   - `AbstractBlock::finalize()`
   - `ConfigurableEnvironmentInterface::addBlockParser()`
   - `Delimiter::setCanClose()`
   - `HtmlRenderer::renderBlock()`
   - `HtmlRenderer::renderBlocks()`
   - `HtmlRenderer::renderInline()`
   - `HtmlRenderer::renderInlines()`
   - `Node::isContainer()`
   - `RegexHelper::REGEX_WHITESPACE`
 - Removed the second `$contents` argument from the `Heading` constructor

[unreleased]: https://github.com/thephpleague/commonmark/compare/1.5...latest
