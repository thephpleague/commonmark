---
layout: default
title: Upgrading from 2.5 to 2.6
description: Guide to upgrading to newer versions of this library
redirect_from: /upgrading/
---

# Upgrading from 2.5 to 2.6

## Custom Delimiter Processors

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
