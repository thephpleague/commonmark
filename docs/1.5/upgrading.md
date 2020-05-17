---
layout: default
title: Upgrading from 1.4 - 1.5
description: Guide to upgrading to newer versions of this library
redirect_from: /upgrading/
---

# Upgrading from 1.4 to 1.5

## Changes

## Deprecations


The `InlineMentionParser` has been deprecated. Use `MentionParser` instead.

### Heading Permalink Slug Generators

The following two classes under `League\CommonMark\Extension\HeadingPermalink` have been deprecated in favor of newer versions:

| `Old Class`                   | `New Class`                            |
| ----------------------------- | -------------------------------------- |
| `Slug\DefaultSlugGenerator`   | `SlugGenerator\DefaultSlugGenerator`   |
| `Slug\SlugGeneratorInterface` | `SlugGenerator\SlugGeneratorInterface` |

The method signature within these have also changed from:

```php
public function createSlug(string $input): string;
```

To:

```php
public function generateSlug(Node $node): string;
```
