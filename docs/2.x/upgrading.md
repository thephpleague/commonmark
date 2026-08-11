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

## Upgrading from 2.9 to 2.10

There are no breaking API changes when upgrading from 2.9 to 2.10, but two constants were deprecated and a few
behaviors changed for integrations which hook into the affected extensions.

### The Table of Contents Is Rendered Once Per Document

In `placeholder` mode the table of contents is now rendered a single time and that one result is shared across every
placeholder, instead of the whole subtree being cloned into each of them.  A custom renderer registered for the
`TableOfContents` node is therefore no longer called once per placeholder, and must return the same markup each time it
is called for a given document.  The first placeholder still receives the `TableOfContents` node itself, so listeners
which locate and reposition that node continue to work.

### Recommended: Bound Table of Contents Placeholders

Every placeholder legitimately renders its own copy of the table of contents, so a document with many headings and many
placeholders can produce output far larger than its input.  If you're parsing untrusted input in `placeholder` mode, we
now recommend setting the new `table_of_contents/max_placeholder_entries` option, which bounds the total number of
entries rendered across all of a document's placeholders; placeholders beyond that budget are left as-is.  It defaults
to `null` (unlimited) for backward compatibility.

### `default_attributes` Accepts a New Configuration Shape

`default_attributes` still accepts the node attribute map on its own, and now also accepts that map paired with a new
`strict_callables` option:

```php
$config = [
    'default_attributes' => [
        'attributes' => [Paragraph::class => ['class' => 'text']],
        'strict_callables' => true,
    ],
];
```

With `strict_callables` enabled only closures and invokable objects are treated as callbacks, so strings and arrays are
always used as literal attribute values.  This keeps a value which happens to share its name with a PHP function - such
as `'class' => 'link'`, `'header'`, `'key'`, `'range'`, or `'current'` - from being invoked instead of used.  A callback
written as a string or array callable can be wrapped with `Closure::fromCallable()`.

Reading the option back has changed shape to match: `$environment->getConfiguration()->get('default_attributes')` now
returns the normalized structure, so read `default_attributes/attributes` to get the node map.  The `strict_callables`
option is itself deprecated, and will be removed in 3.0 when only closures and invokable objects will ever be treated
as callbacks.

### Custom Unique Slug Normalizers Are No Longer Wrapped

A custom `UniqueSlugNormalizerInterface` implementation is now used directly rather than being wrapped by the built-in
`UniqueSlugNormalizer`.  Such implementations now receive the `clearHistory()` calls the interface documents - without
which slug history leaked between documents when `slug_normalizer/unique` was set to `'document'` - and are trusted to
enforce uniqueness themselves, so the extra deduplication the wrapper applied on top of them is no longer performed.

### Deprecated: Anchored `RegexHelper` Constants

`RegexHelper::PARTIAL_LINK_TITLE` and `RegexHelper::REGEX_LINK_DESTINATION_BRACES` are deprecated.  Use the new
unanchored `RegexHelper::PARTIAL_LINK_TITLE_UNANCHORED` and `RegexHelper::PARTIAL_LINK_DESTINATION_BRACES` fragments
with an anchor of your own choosing instead.

## Upgrading from 2.8 to 2.9

There are no breaking API changes when upgrading from 2.8 to 2.9, but several security fixes change the output of
certain inputs.

### Obfuscated Link Schemes Are Now Blocked

When `allow_unsafe_links` is `false`, the unsafe link filter now detects dangerous schemes obfuscated with embedded
tabs, newlines, or leading control characters (such as `java<TAB>script:`).  Links which browsers would have executed
but which previously slipped through the filter are now stripped.  No safe URL is affected by this change.

### Duplicate Footnote Definitions Are Discarded

If the same footnote label is defined more than once, only the first definition is used and the rest are removed from
the document.  Previously every duplicate was rendered in place, and each one claimed the label's full list of
backrefs.  This matches how duplicate link reference definitions are already resolved.

### XML Output Is Indented Up to a Maximum Depth

`XmlRenderer` now stops adding indentation beyond the new `xml/max_indentation_level` option (default: `16`).  Elements
nested more deeply are still rendered in full, so the XML remains well-formed and semantically identical - only the
leading whitespace differs.  Set the option to a large value to restore the old behavior, or to `0` for compact output.

### Footnote Backrefs Moved to a Single Document Data Key

`NumberFootnotesListener` now stores all footnote backrefs under a single `footnote/backrefs` key in the document data
instead of writing one key per footnote destination.  This only matters if you were reading those keys directly; the
rendered output is unchanged.

### Very Long Shortcut Reference Links No Longer Resolve

Shortcut and collapsed reference links (`[label]` and `[label][]`) now apply the spec's 999-character limit on link
labels when resolving the label, matching the limit already enforced when parsing reference definitions and when
resolving the `[text][label]` form.  A label longer than 999 characters which collapsed to a shorter, defined label
once its whitespace was normalized will no longer resolve, and is left as literal text instead.  This matches the
behavior of the reference `cmark` implementation.

### Recommended: Set a Maximum Nesting Level

If you're parsing untrusted input, we now recommend setting `max_nesting_level` to `100`.  See the
[security documentation](/2.x/security/) for details.

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
