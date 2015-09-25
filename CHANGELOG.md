# Change Log
All notable changes to this project will be documented in this file.
Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [Unreleased][unreleased]

## [0.11.3] - 2015-09-25
## Fixed
 - Reset container after closing containing lists (#183; jgm/commonmark.js#67)
   - The temporary fix from 0.11.2 was reverted

## [0.11.2] - 2015-09-23
## Fixed
 - Fixed parser checking acceptsLines on the wrong element (#183)

## [0.11.1] - 2015-09-22
### Changed
 - Tightened up some loose comparisons

### Fixed
 - Fixed missing "bin" directive in composer.json
 - Updated a docblock to match recent changes to method parameters

### Removed
 - Removed unused variable from within QuoteProcessor's closure

## [0.11.0] - 2015-09-19
### Added
 - Added new `Node` class, which both `AbstractBlock` and `AbstractInline` extend from (#169)
 - Added a `NodeWalker` and `NodeWalkerEvent` to traverse the AST without using recursion
 - Added new `InlineContainer` interface for blocks
 - Added new `getContainer()` and `getReferenceMap()` methods to `InlineParserContext`
 - Added `iframe` to whitelist of HTML block tags (as per spec)
 - Added `./bin/commonmark` for converting Markdown at the command line

### Changed
 - Bumped spec target version to 0.22
 - Revised AST to use a double-linked list (#169)
 - `AbstractBlock` and `AbstractInline` both extend from `Node`
   - Sub-classes must implement new `isContainer()` method
 - Other major changes to `AbstractBlock`:
   - `getParent()` is now `parent()`
   - `setParent()` now expects a `Node` instead of an `AbstractBlock`
   - `getChildren()` is now `children()`
   - `getLastChild()` is now `lastChild()`
   - `addChild()` is now `appendChild()`
 - `InlineParserContext` is constructed using the container `AbstractBlock` and the document's `RefereceMap`
   - The constructor will automatically create the `Cursor` using the container's string contents
 - `InlineParserEngine::parse` now requires the `Node` container and the document's `ReferenceMap` instead of a `ContextInterface` and `Cursor`
 - Changed `Delimiter` to reference the actual inline `Node` instead of the position
   - The `int $pos` protected member and constructor arg is now `Node $node`
   - Use `getInlineNode()` and `setInlineNode()` instead of `getPos()` and `setPos()`
 - Changed `DocParser::processInlines` to use a `NodeWalker` to iterate through inlines
   - Walker passed as second argument instead of `AbstractBlock`
   - Uses a `while` loop instead of recursion to traverse the AST
 - `Image` and `Link` now only accept a string as their second argument
 - Refactored how `CloseBracketParser::parse()` works internally
 - `CloseBracketParser::createInline` no longer accepts label inlines
 - Disallow list item starting with multiple blank lines (see jgm/CommonMark#332)
 - Modified `AbstractBlock::setLastLineBlank()`
   - Functionality moved to `AbstractBlock::shouldLastLineBeBlank()` and new `DocParser::setAndPropagateLastLineBlank()` method
   - `AbstractBlock::setLastLineBlank()` is now a setter method for `AbstractBlock::$lastLineBlank`
 - `AbstractBlock::handleRemainingContents()` is no longer abstract
   - A default implementation is provided
   - Removed duplicate code from sub-classes which used the default implementation - they'll just use the parent method from now on

### Fixed
 - Fixed logic error in calculation of offset (see jgm/commonmark.js@94053a8)
 - Fixed bug where `DocParser` checked the wrong method to determine remainder handling behavior
 - Fixed bug where `HorizontalRuleParser` failed to advance the cursor beyond the parsed horizontal rule characters
 - Fixed `DocParser` not ignoring the final newline of the input (like the reference parser does)

### Removed
 - Removed `Block\Element\AbstractInlineContainer`
   - Extend `AbstractBlock` and implement `InlineContainer` instead
   - Use child methods instead of `getInlines` and `setInlines`
 - Removed `AbstractBlock::replaceChild()`
   - Call `Node::replaceWith()` directly the child node instead
 - Removed the `getInlines()` method from `InlineParserContext`
   - Add parsed inlines using `$inlineContext->getContainer()->appendChild()` instead of `$inlineContext->getInlines()->add()`
 - Removed the `ContextInterface` argument from `AbstractInlineParser::parse()` and `InlineParserEngine::parseCharacter`
 - Removed the first `ArrayCollection $inlines` argument from `InlineProcessorInterface::processInlines()`
 - Removed `CloseBracketParser::nullify()`
 - Removed `pre` from rule 6 of HTML blocks (see jgm/CommonMark#355)

## [0.10.0] - 2015-07-25
### Added
 - Added parent references to inline elements (#124)
 - Added smart punctuation extension (#134)
 - Added HTML block types
 - Added indentation caching to the cursor
 - Added automated code style checks (#133)
 - Added support for tag attributes in renderers (#101, #165)

### Changed
 - Bumped spec target version to 0.21
 - Revised HTML block parsing to conform to new spec (jgm/commonmark.js@99bd473)
 - Imposed 9-digit limit on ordered list markers, per spec
 - Allow non-initial hyphens in html tag names (jgm/CommonMark#239)
 - Updated list of block tag names
 - Changed tab/indentation handling to meet the new spec behavior
 - Modified spec tests to show spaces and tabs in test results
 - Replaced `HtmlRendererInterface` with `ElementRendererInterface` (#141)
 - Removed the unnecessary `trim()` and string cast from `ListItemRenderer`

### Fixed
 - Fixed link reference definition edge case (#120)
 - Allow literal (non-escaping) backslashes in link destinations (#118)
 - Allow backslash-escaped backslashes in link labels (#119)
 - Allow link labels up to 999 characters (per the spec)
 - Properly split on whitespace when determining code block class (jgm/commonmark.js#54)
 - Fixed code style issues (#132, #133, #151, #152)
 - Fixed wording for invalid inline exception (#136)

### Removed
 - Removed the advance-by-one optimization due to added cursor complexity

## [0.9.0] - 2015-06-18
### Added
 - Added public $data array to block elements (#95)
 - Added `isIndented` helper method to `Cursor`
 - Added a new `Converter` base class which `CommonMarkConverter` extends from (#105)

### Changed
 - Bumped spec target version to 0.20 (#112)
 - Renamed ListBlock::$data and ListItem::$data to $listData
 - Require link labels to contain non-whitespace (jgm/CommonMark#322)
 - Use U+FFFD for entities resolving to 0 (jgm/CommonMark#323)
 - Moved `IndentedCodeParser::CODE_INDENT_LEVEL` to `Cursor::INDENT_LEVEL`
 - Changed arrays to short syntax (#116)
 - Improved efficiency of DelimiterStack iteration (jgm/commonmark.js#43)

### Fixed
 - Fixed open block tag followed by newline not being recognized (jgm/CommonMark#324)
 - Fixed indented lists sometimes being parsed incorrectly (jgm/commonmark.js#42)

## [0.8.0] - 2015-04-29
### Added
 - Allow swapping built-in renderers without using their fully qualified names (#84)
 - Lots of unit tests (for existing code)
 - Ability to include arbitrary functional tests in addition to spec-based tests

### Changed
 - Dropped support for PHP 5.3 (#64 and #76)
 - Bumped spec target version to 0.19
 - Made the AbstractInlineContainer be abstract
 - Moved environment config. logic into separate class

### Fixed
 - Fixed underscore emphasis to conform to spec changes (jgm/CommonMark#317)

### Removed
 - Removed PHP 5.3 workaround (see commit 5747822)
 - Removed unused AbstractWebResource::setUrl() method
 - Removed unnecessary check for hrule when parsing lists (#85)

## [0.7.2] - 2015-03-08
### Changed
 - Bumped spec target version to 0.18

### Fixed
 - Fixed broken parsing of emphasized text ending with a '0' character (#81)

## [0.7.1] - 2015-03-01
### Added
 - All references can now be obtained from the `ReferenceMap` via `listReferences()` (#73)
 - Test against PHP 7.0 (nightly) but allow failures

### Changed
 - ListData::$start now defaults to null instead of 0 (#74)
 - Replace references to HtmlRenderer with new HtmlRendererInterface

### Fixed
 - Fixed 0-based ordered lists starting at 1 instead of 0 (#74)
 - Fixed errors parsing multi-byte characters (#78 and #79)

## [0.7.0] - 2015-02-16
### Added
 - More unit tests to increase code coverage

### Changed
 - Enabled the InlineParserEngine to parse several non-special characters at once (performance boost)
 - NewlineParser no longer attempts to parse spaces; look-behind is used instead (major performance boost)
 - Moved closeUnmatchedBlocks into its own class
 - Image and link elements now extend AbstractInlineContainer; label data is stored via $inlineContents instead
 - Renamed AbstractInlineContainer::$inlineContents and its getter/setter

### Removed
 - Removed the InlineCollection class
 - Removed the unused ArrayCollection::splice() method
 - Removed impossible-to-reach code in Cursor::advanceToFirstNonSpace
 - Removed unnecessary test from the InlineParserEngine
 - Removed unnecessary/unused RegexHelper::getMainRegex() method

## [0.6.1] - 2015-01-25
### Changed
 - Bumped spec target version to 0.17
 - Updated emphasis parsing for underscores to prevent intra-word emphasis
 - Deferred closing of fenced code blocks

## [0.6.0] - 2015-01-09
### Added
 - Bulk registration of parsers/renderers via extensions (#45)
 - Proper UTF-8 support, especially in the Cursor; mbstring extension is now required (#49)
 - Environment is now configurable; options can be accessed in its parsers/renderers (#56)
 - Added some unit tests

### Changed
 - Bumped spec target version to 0.15 (#50)
 - Parsers/renderers are now lazy-initialized (#52)
 - Some private elements are now protected for easier extending, especially on Element classes (#53)
 - Renderer option keys changed from camelCase to underscore_case (#56)
 - Moved CommonMark parser/render definitions into CommonMarkCoreExtension

### Fixed
 - Improved parsing of emphasis around punctuation
 - Improved regexes for CDATA and HTML comments
 - Fixed issue with HTML content that is considered false in loose comparisons, like `'0'` (#55)
 - Fixed DocParser trying to add empty strings to closed containers (#58)
 - Fixed incorrect use of a null parameter value in the HtmlElementTest

### Removed
 - Removed unused ReferenceDefinition* classes (#51)
 - Removed UnicodeCaseFolder in favor of mb_strtoupper

## [0.5.1] - 2014-12-27
### Fixed
 - Fixed infinite loop and link-in-link-in-image parsing (#37)

### Removed
 - Removed hard dependency on mbstring extension; workaround used if not installed (#38)

## [0.5.0] - 2014-12-24
### Added
 - Support for custom directives, parsers, and renderers

### Changed
 - Major refactoring to de-couple directives from the parser, support custom directive functionality, and reduce complexity
 - Updated references to stmd.js in README and docblocks
 - Modified CHANGELOG formatting
 - Improved travis configuration
 - Put tests in autoload-dev

### Fixed
 - Fixed CommonMarkConverter re-creating object each time new text is converted (#26)

### Removed
 - Removed HtmlRenderer::render() (use the renderBlock method instead)
 - Removed dependency on symfony/options-resolver (fixes #20)

## [0.4.0] - 2014-12-15
### Added
 - Added some missing copyright info

### Changed
 - Changed namespace to League\CommonMark
 - Made compatible with spec version 0.13
 - Moved delimiter stack functionality into separate class

### Fixed
 - Fixed regex which caused HHVM tests to fail

## [0.3.0] - 2014-11-28
### Added
 - Made renderer options configurable (issue #7)

### Changed
 - Made compatible with spec version 0.12
 - Stack-based parsing now used for emphasis, links and images
 - Protected some of the internal renderer methods which shouldn't have been `public`
 - Minor code clean-up (including PSR-2 compliance)

### Removed
 - Removed unnecessary distinction between ATX and Setext headers

## [0.2.1] - 2014-11-09
### Added
 - Added simpler string replacement to a method

### Changed
 - Removed "is" prefix from boolean methods
 * Updated to latest version of PHPUnit
 * Target specific spec version

## [0.2.0] - 2014-11-09
### Changed
 - Mirrored significant changes and improvements from stmd.js
 - Made compatible with spec version 0.10
 - Updated location of JGM's repository
 - Allowed HHVM tests to fail without affecting overall build success

### Removed
 - Removed composer.lock
 - Removed fixed reference to jgm/stmd@0275f34

## [0.1.2] - 2014-09-28
### Added
 - Added performance benchmarking tool (issue #2)
 - Added more badges to the README

### Changed
 - Fix JS -> PHP null judgement (issue #4)
 - Updated phpunit dependency

## [0.1.1] - 2014-09-08
### Added
 - Add anchors to regexes

### Changed
 - Updated target spec (now compatible with jgm/stmd:spec.txt @ 2cf0750)
 - Adjust HTML output for fenced code
 - Adjust block-level tag regex (remove "br", add "iframe")
 - Fix incorrect handling of nested emphasis

## 0.1.0
### Added
 - Initial commit (compatible with jgm/stmd:spec.txt @ 0275f34)

[unreleased]: https://github.com/thephpleague/commonmark/compare/0.11.3...HEAD
[0.11.3]: https://github.com/thephpleague/commonmark/compare/0.11.2...0.11.3
[0.11.2]: https://github.com/thephpleague/commonmark/compare/0.11.1...0.11.2
[0.11.1]: https://github.com/thephpleague/commonmark/compare/0.11.0...0.11.1
[0.11.0]: https://github.com/thephpleague/commonmark/compare/0.10.0...0.11.0
[0.10.0]: https://github.com/thephpleague/commonmark/compare/0.9.0...0.10.0
[0.9.0]: https://github.com/thephpleague/commonmark/compare/0.8.0...0.9.0
[0.8.0]: https://github.com/thephpleague/commonmark/compare/0.7.2...0.8.0
[0.7.2]: https://github.com/thephpleague/commonmark/compare/0.7.1...0.7.2
[0.7.1]: https://github.com/thephpleague/commonmark/compare/0.7.0...0.7.1
[0.7.0]: https://github.com/thephpleague/commonmark/compare/0.6.1...0.7.0
[0.6.1]: https://github.com/thephpleague/commonmark/compare/0.6.0...0.6.1
[0.6.0]: https://github.com/thephpleague/commonmark/compare/0.5.1...0.6.0
[0.5.1]: https://github.com/thephpleague/commonmark/compare/0.5.0...0.5.1
[0.5.0]: https://github.com/thephpleague/commonmark/compare/0.4.0...0.5.0
[0.4.0]: https://github.com/thephpleague/commonmark/compare/0.3.0...0.4.0
[0.3.0]: https://github.com/thephpleague/commonmark/compare/0.2.1...0.3.0
[0.2.1]: https://github.com/thephpleague/commonmark/compare/0.2.0...0.2.1
[0.2.0]: https://github.com/thephpleague/commonmark/compare/0.1.2...0.2.0
[0.1.2]: https://github.com/thephpleague/commonmark/compare/0.1.1...0.1.2
[0.1.1]: https://github.com/thephpleague/commonmark/compare/0.1.0...0.1.1
