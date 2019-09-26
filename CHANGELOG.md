# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased][unreleased]

## [2.1.0] - 2019-09-26

### Fixed

 - Fixed attributes being double-escaped (#31)

## [2.0.0] - 2019-07-13

### Changed

 - Changed the namespace to `League\CommonMark\Ext\Table`
 - Renamed `TableRows` to `TableSection`
 - Made the parser and renderers `final`

### Removed

 - Removed the unused `TableRow::handleRemainingContents()` function

## [1.0.0] - 2019-07-09
### Changed

 - Increased minimum PHP version to 7.1
 - Updated to the commonmark 0.19 and 1.0

### Removed

 - Removed support for commonmark 0.16, 0.17 and 0.18

## [0.9.0] - 2018-11-28
### Changed

 - Allowed the commonmark 0.18

## [0.8.0] - 2018-01-24
### Changed

 - Replaced align attribute with text-align style

## [0.7.1] - 2018-01-23
### Fixed

 - Fixed undefined method in commonmark 0.17

## [0.7.0] - 2018-01-09
### Changed

 - Increased minimum PHP version to 5.6
 - Updated to the commonmark 0.16 and 0.17 API
 
### Fixed

 - Fixed a problem with parsing whitespaces at the end of line
 
### Removed

 - Removed support for commonmark 0.14 and 0.15 API

## [0.6.1] - 2017-01-11
### Fixed

 - Fixed parsing of one column tables

## [0.6.0] - 2016-09-26
### Changed
 - Updated to the commonmark 0.15 API
 - Moved twig template to [webuni/commonmark-twig-renderer](https://packagist.org/packages/webuni/commonmark-twig-renderer)

## [0.5.0] - 2016-07-13
### Added

 - Added support for table caption (MultiMarkdown)
 - Added a template for twig renderer

### Changed

 - Updated to the commonmark 0.14 API

## [0.4.3] - 2016-01-14
### Added

 - Added support for commonmark 0.13 API

## [0.4.2] - 2015-11-05
### Added

 - Added support for commonmark 0.12 API

## [0.4.1] - 2015-09-22
### Added

 - Added missing tests

## [0.4.0] - 2015-09-21
### Changed

 - Updated to the new commonmark 0.11 API

## [0.3.0] - 2015-07-27
### Added

 - Added support for custom attributes in renderers

## [0.2.0] - 2015-07-27
### Changed

 - Updated to the new commonmark 0.10 API

## [0.1.0] - 2015-06-24
### Added
 - Implemented GFM tables

[unreleased]: https://github.com/thephpleague/commonmark-ext-table/compare/v2.1.0...HEAD
[2.1.0]: https://github.com/thephpleague/commonmark-ext-table/compare/v2.0.0...v2.1.0
[2.0.0]: https://github.com/thephpleague/commonmark-ext-table/compare/1.0.0...v2.0.0
[1.0.0]: https://github.com/thephpleague/commonmark-ext-table/compare/0.9.0...1.0.0
[0.9.0]: https://github.com/thephpleague/commonmark-ext-table/compare/0.8.0...0.9.0
[0.8.0]: https://github.com/thephpleague/commonmark-ext-table/compare/0.7.1...0.8.0
[0.7.1]: https://github.com/thephpleague/commonmark-ext-table/compare/0.7.0...0.7.1
[0.7.0]: https://github.com/thephpleague/commonmark-ext-table/compare/0.6.1...0.7.0
[0.6.1]: https://github.com/thephpleague/commonmark-ext-table/compare/0.6.0...0.6.1
[0.6.0]: https://github.com/thephpleague/commonmark-ext-table/compare/0.5.0...0.6.0
[0.5.0]: https://github.com/thephpleague/commonmark-ext-table/compare/0.4.3...0.5.0
[0.4.3]: https://github.com/thephpleague/commonmark-ext-table/compare/0.4.2...0.4.3
[0.4.2]: https://github.com/thephpleague/commonmark-ext-table/compare/0.4.1...0.4.2
[0.4.1]: https://github.com/thephpleague/commonmark-ext-table/compare/0.4.0...0.4.1
[0.4.0]: https://github.com/thephpleague/commonmark-ext-table/compare/0.3.0...0.4.0
[0.3.0]: https://github.com/thephpleague/commonmark-ext-table/compare/0.2.0...0.3.0
[0.2.0]: https://github.com/thephpleague/commonmark-ext-table/compare/0.1.0...0.2.0
[0.1.0]: https://github.com/thephpleague/commonmark-ext-table/commits/0.1.0
