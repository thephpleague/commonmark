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
