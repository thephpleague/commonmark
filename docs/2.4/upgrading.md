---
layout: default
title: Upgrading from 2.3 to 2.4
description: Guide to upgrading to newer versions of this library
---

# Upgrading from 2.3 to 2.4

## Exception Changes

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
