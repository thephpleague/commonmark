---
layout: default
title: Upgrading from 2.1 to 2.2
description: Guide to upgrading to newer versions of this library
---

# Upgrading from 2.1 to 2.2

## Deprecation of `MarkdownConverterInterface`

The `MarkdownConverterInterface` and its `convertToHtml()` method were deprecated in 2.2.0 and will be removed in 3.0.0.
You should switch your implementations to use `ConverterInterface` and `convert()` instead which provide the same behavior.
