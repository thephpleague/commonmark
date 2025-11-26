---
layout: default
title: Upgrading to Newer Versions
description: Guide to upgrading to newer versions of this library
redirect_from:
  - /upgrading/
  - /2.0/upgrading/
  - /2.1/upgrading/
  - /2.2/upgrading/
  - /2.3/upgrading/
  - /2.4/upgrading/
  - /2.5/upgrading/
  - /2.6/upgrading/
  - /2.7/upgrading/
---

# Upgrading to Newer Versions

## Upgrading from 2.7 to 2.8

There are no breaking changes when upgrading from 2.7 to 2.8.

## Upgrading from 2.6 to 2.7

There are no breaking changes when upgrading from 2.6 to 2.7.

## Upgrading from 2.5 to 2.6

### `max_delimiters_per_line` Configuration Option

The `max_delimiters_per_line` configuration option was added in 2.6 to help protect against malicious input that could
cause excessive memory usage or denial of service attacks.  It defaults to `PHP_INT_MAX` (no limit) for backwards
compatibility, which is safe when parsing trusted input. However, if you're parsing untrusted input from users, you
should probably set this to a reasonable value (somewhere between `100` and `1000`) to protect against malicious inputs.

### Custom Delimiter Processors

If you're implementing a custom delimiter processor, and `getDelimiterUse()` has more logic than just a
simple `return` statement, you should implement `CacheableDelimiterProcessorInterface` instead of
`DelimiterProcessorInterface` to improve performance and avoid possible quadratic performance issues.

`DelimiterProcessorInterface` has a `getDelimiterUse()` method that tells the engine how many characters from the
matching delimiters should be consumed. Simple processors usually always return a hard-coded integer like `1` or `2`.
However, some more advanced processors may need to examine the opening and closing delimiters and perform additional
logic to determine whether they should be fully or partially consumed.  Previously, these results could not be safely
cached, resulting in possible quadratic performance issues.

In 2.6, the `CacheableDelimiterProcessorInterface` was introduced to allow these "dynamic" processors to be safely
cached. It requires a new `getCacheKey()` method that returns a string that uniquely identifies the combination of
factors considered when determining the delimiter use.  This key is then used to cache the results of the search for
a matching delimiter.

## Upgrading from 2.4 to 2.5

These are no significant changes since 2.4.

## Upgrading from 2.3 to 2.4

### Exception Changes

Prior to 2.4.0, this library did a poor job of using appropriate exception types and documenting which exceptions could
be thrown. For example, all of the main interfaces said that only `RuntimeException` could be thrown, but in reality
other exceptions like `LogicException` or `InvalidArgumentException` could be thrown in some cases!

This inconsistent behavior and inaccurate documentation has been fixed in 2.4.0 by:

- Adding a new `CommonMarkException` interface implemented by all exceptions thrown by this library
- Adding several new exception types that implement that interface while also extending from the same base exception
  type as that would have been previously thrown.
- Fixing incorrect docblocks about the exception types being thrown

If you were previously catching exceptions thrown by this library in your code, you should consider changing your
`catch` blocks to either catch `CommonMarkException` (for all exceptions) or one of the exception types under the
`League\CommonMark\Exception` namespace.

## Upgrading from 2.2 to 2.3

### Avoid deprecated interface

`MarkdownRendererInterface` has been deprecated and will be removed in the next major version. Please use `DocumentRendererInterface` instead.

## Upgrading from 2.1 to 2.2

### Deprecation of `MarkdownConverterInterface`

The `MarkdownConverterInterface` and its `convertToHtml()` method were deprecated in 2.2.0 and will be removed in 3.0.0.
You should switch your implementations to use `ConverterInterface` and `convert()` instead which provide the same behavior.

## Upgrading from 2.0 to 2.1

No changes or deprecations were made that require changes to upgrade to the new version.

## Upgrading from 1.6 to 2.0

Version 2.0 contains **lots** of changes throughout the library.  We've split the upgrade guide into three sections to help you better identify the changes that are most relevant to you:

### For Consumers

The [upgrade guide for consumers](/2.x/upgrading/consumers/) is relevant for developers who use this library as-is to perform basic conversion of Markdown to HTML.  You might enable some extensions or tweak the configuration settings, but you don't write your own custom parsers or anything like that.  This condensed upgrade guide therefore only covers the most obvious changes that might impact your usage of this library.

### For Integrators

If you develop open-source software that uses this library, read the [upgrade guide for integrators](/2.x/upgrading/integrators/).  It contains all of the information from the Consumer guide above, but with additional details that may be relevant to you.

### For Developers

The [upgrade guide for developers](/2.x/upgrading/developers/) is aimed at developers who create custom extensions/parsers/renderers and need to know about all of the under-the-hood changes in 2.x.  It is the most comprehensive guide, containing all of the information from the two guides above, and even more details about the under-the-hood changes that likely impact your customizations.
