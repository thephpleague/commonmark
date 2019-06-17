# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased][unreleased]

## [0.3.1] - 2019-06-17

### Fixed

 - Fixed extension incorrectly double-linking standard CommonMark autolinks (#12)

## [1.0.0-beta2] - 2019-06-05

### Changed

 - Made extension compatible with `league/commonmark` 1.0.0-beta4

## [1.0.0-beta1] - 2019-05-27

### Changed

 - Marked extension as compatible with `league/commonmark` 1.0.0-beta1

## [0.3.0] - 2019-04-10

### Changed

 - Made extension compatible with `league/commonmark` 0.19

## [0.2.1] - 2019-03-28

### Changed

 - Increased the minimum `league/commonmark` version due to a now-fixed bug with `Text` collapsing (#10)

## [0.2.0] - 2019-03-16

This release brings the email and URL autolink processors into alignment with the official GFM spec.

### Added

 - Added full support for Github Flavored Markdown (GFM) autolinking
 - Added some optimizations

### Changed

 - Made `ftp` a default protocol
 - Revised the email regex to match the GFM spec

### Fixed

 - Fixed bug where links at the start or end of lines failed to be parsed

## [0.1.0] - 2019-03-14

Initial release!

[unreleased]: https://github.com/thephpleague/commonmark-ext-autolink/compare/v1.0.0-beta2...HEAD
[1.0.0-beta2]: https://github.com/thephpleague/commonmark-ext-autolink/compare/v1.0.0-beta1...v1.0.0-beta2
[1.0.0-beta1]: https://github.com/thephpleague/commonmark-ext-autolink/compare/v0.3.0...v1.0.0-beta1
[0.3.1]: https://github.com/thephpleague/commonmark-ext-autolink/compare/v0.3.0...v0.3.1
[0.3.0]: https://github.com/thephpleague/commonmark-ext-autolink/compare/v0.2.1...v0.3.0
[0.2.1]: https://github.com/thephpleague/commonmark-ext-autolink/compare/v0.2.0...v0.2.1
[0.2.0]: https://github.com/thephpleague/commonmark-ext-autolink/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/thephpleague/commonmark-ext-autolink/commits/v0.1.0
