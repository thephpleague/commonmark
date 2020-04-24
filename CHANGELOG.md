# Change Log
All notable changes to this project will be documented in this file.
Updates should follow the [Keep a CHANGELOG](https://keepachangelog.com/) principles.

## [Unreleased][unreleased]

See <https://commonmark.thephpleague.com/2.0/upgrading/> for detailed information on upgrading to version 2.0.

### Added

 - Added new `HtmlFilter` utility class

### Changed

 - Moved classes into different namespaces
 - Renamed `ElementRendererInterface` to `NodeRendererInterface`
 - Moved and renamed the following constants:
   - `EnvironmentInterface::HTML_INPUT_ALLOW` is now `HtmlFilter::ALLOW`
   - `EnvironmentInterface::HTML_INPUT_ESCAPE` is now `HtmlFilter::ESCAPE`
   - `EnvironmentInterface::HTML_INPUT_STRIP` is now `HtmlFilter::STRIP`
 - Added missing return types to virtually every class and interface method
 - Added `void` return types to all methods that don't return anything
 - Several methods which previously returned `$this` now return `void`
   - `Delimiter::setPrevious()`
   - `Node::replaceChildren()`
   - `Context::setTip()`
   - `Context::setContainer()`
   - `Context::setBlocksParsed()`
   - `AbstractStringContainer::setContent()`
   - `AbstractWebResource::setUrl()`

### Removed

 - Removed support for PHP 7.1
 - Removed all previously-deprecated functionality:
   - Removed the `Converter` class and `ConverterInterface`
   - Removed the `bin/commonmark` script
   - Removed the `Html5Entities` utility class
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
 - Removed the unused `Delimiter::setCanClose()` method

[unreleased]: https://github.com/thephpleague/commonmark/compare/1.4...master
