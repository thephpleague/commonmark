---
layout: default
title: Upgrading from 1.3 - 1.4
description: Guide to upgrading to newer versions of this library
---

# Upgrading from 1.3 to 1.4

There are no major changes introduced in 1.4.

## Deprecations

Several things have been deprecated in 1.4 - they'll continue to work, but consider using alternatives to make your code easier to upgrade to 2.0 when these deprecated things are removed.

### `bin/console` command

This command has been buggy to test and is relatively unpopular, so this will be removed in 2.0. If you need this type of functionality, consider writing your own script with a Converter/Environment configured exactly how you want it.
