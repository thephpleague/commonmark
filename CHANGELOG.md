# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased][unreleased]

## [1.0.0] - 2019-06-29

No code changes have been introduced since 1.0.0-beta2.

## [1.0.0-beta2] - 2019-05-27

### Changed

 - Updated extension to work with `league/commonmark` 1.0.0-beta3

## [1.0.0-beta1] - 2019-05-27

### Changed

 - Updated to the next version of `league/commonmark`
 - Refactored pretty much everything to use the new delimiter processing functionality
 - The `Strikethrough` node now extends from `AbstractInline`

### Removed
 - Removed `StrikethroughParser` as we're now leveraging delimiter processors and no longer need a dedicated parser

## [0.4.0] - 2019-04-09

We split this extension out of the old `uafrica/commonmark-ext` library for this release.

### Changed

 - Changed the project name and namespace
 - Updated to `league/commonmark` v0.19
 - Made most of the classes `final`

# Older Releases

These older releases come from the original `uafrica/commonmark-ext` library.

## [0.3.0] - 2019-01-08

### Changed

 - Updated to `league/commonmark` v0.18

## [0.2.0] - 2018-05-14

### Changed

 - Updated to `league/commonmark` v0.17

[unreleased]: https://github.com/thephpleague/commonmark-ext-strikethrough/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/thephpleague/commonmark-ext-strikethrough/compare/v1.0.0-beta2...v1.0.0
[1.0.0-beta2]: https://github.com/thephpleague/commonmark-ext-strikethrough/compare/v1.0.0-beta1...v1.0.0-beta2
[1.0.0-beta1]: https://github.com/thephpleague/commonmark-ext-strikethrough/compare/v0.4.0...v1.0.0-beta1
[0.4.0]: https://github.com/thephpleague/commonmark-ext-strikethrough/compare/v0.3.0...v0.4.0
[0.3.0]: https://github.com/thephpleague/commonmark-ext-strikethrough/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/thephpleague/commonmark-ext-strikethrough/compare/v0.1.1...v0.2.0
