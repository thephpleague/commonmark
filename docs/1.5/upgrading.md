---
layout: default
title: Upgrading from 1.4 - 1.5
description: Guide to upgrading to newer versions of this library
redirect_from: /upgrading/
---

# Upgrading from 1.4 to 1.5

## Changes

The `ExternalLink` extension will now apply a `nofollow` option to the `<a rel="...">` tag by default.  If you don't want this behavior, set the `external_link/nofollow` option to an empty string (`''`).

## Deprecations
