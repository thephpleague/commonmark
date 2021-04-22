---
layout: default
title: Upgrading from 1.x - 1.3
description: Guide to upgrading to newer versions of this library
---

# Upgrading from 1.x to 1.3

There are no breaking changes introduced in 1.3, but we did move most of our extensions into the core library.

## GitHub-Flavored Markdown and other official extensions

Previously, anyone wanting GFM support had to install additional libraries like `league/commonmark-extras`.  This is no longer required as **GFM support is now built into this library!**  This is also true for other official extensions like `league/commonmark-ext-inlines-only`.

If you were previously using any of the `league/commonmark-ext*` libraries:

- Upgrade to `league/commonmark` 1.3+
- Replace any reference to the old extensions in your code with the new ones
  - (In most cases, simply search your code for `League\CommonMark\Ext\` and replace it with `League\CommonMark\Extension\`)
- Remove those `league/commonmark-ext*` dependencies from Composer

See the [GitHub-Flavored Markdown extension](/1.3/extensions/github-flavored-markdown/) documentation for more information on using this extension.  Additional details can also be found in [Colin O'Dell's blog post](https://www.colinodell.com/blog/202002/league-commonmark-130-adds-full-gfm-support).
