---
layout: default
title: Upgrading from 0.19 to 0.20
---

# Upgrading from 0.19 to 0.20

### Inline Processors

The "inline processor" functionality has been removed and replaced with a proper "delimiter processor" feature geared specifically towards dealing with delimiters (which is what the previous implementation tried to do - poorly).

No direct upgrade path exists as this implementation was not widely used, or only used for implementing delimiter processing.  If you fall in the latter category, simply leverage the new functionality instead.  Otherwise, if you have another good use case for inline processors, please let us know in the issue tracker.

## `DocParser`

The `DocParser` class is now `final` as it was never intended to be extended, especially given how so much logic was in `private` methods.  Any custom implementations should implement the new `DocParserInterface` interface instead.

Additionally, the `getEnvironment()` method has been deprecated and excluded from that new interface, as it was only used internally by the `DocParser` and other better ways exist to obtain an environment where needed.

## `AbstractInlineContainer`

The `AbstractInlineContainer` class added an unnecessary level of inheritance and was therefore deprecated. If you previously extended this class, you should now extend from `AbstractInline` and override `isContainer()` to return `true`.

## `AdjoiningTextCollapser`

The `AdjoiningTextCollapser` is an internal class used to combine multiple `Text` elements into one.  If you were using this yourself (unlikely) you'll need to refer to its new name instead: `AdjacentTextMerger`. And if you previously used `collapseTextNodes()` you'll want to switch to using `mergeChildNodes()` instead.
