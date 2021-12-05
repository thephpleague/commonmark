---
layout: default
title: Upgrading from 1.6 to 2.0
description: Guide to upgrading to newer versions of this library
---

# Upgrading from 1.6 to 2.0

Version 2.0 contains **lots** of changes throughout the library.  We've split the upgrade guide into three sections to help you better identify the changes that are most relevant to you:

## For Consumers

The [upgrade guide for consumers](/2.0/upgrading/consumers/) is relevant for developers who use this library as-is to perform basic conversion of Markdown to HTML.  You might enable some extensions or tweak the configuration settings, but you don't write your own custom parsers or anything like that.  This condensed upgrade guide therefore only covers the most obvious changes that might impact your usage of this library.

## For Integrators

If you develop open-source software that uses this library, read the [upgrade guide for integrators](/2.0/upgrading/integrators/).  It contains all of the information from the Consumer guide above, but with additional details that may be relevant to you.

## For Developers

The [upgrade guide for developers](/2.0/upgrading/developers/) is aimed at developers who create custom extensions/parsers/renderers and need to know about all of the under-the-hood changes in 2.x.  It is the most comprehensive guide, containing all of the information from the two guides above, and even more details about the under-the-hood changes that likely impact your customizations.
