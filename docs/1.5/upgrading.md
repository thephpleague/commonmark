---
layout: default
title: Upgrading from 1.4 - 1.5
description: Guide to upgrading to newer versions of this library
redirect_from: /upgrading/
---

# Upgrading from 1.4 to 1.5

## Changes

## Deprecations

`Reference::normalizeReference()` has been deprecated. Use `TextNormalizer::normalize()` instead.

The `InlineMentionParser` has been deprecated. Use `MentionParser` instead.

### Heading Permalink Slug Generators

The following two classes have been deprecated in favor of more-generic text normalizers:

| `Old Class`                                              | `New Class`                          |
| -------------------------------------------------------- | ------------------------------------ |
| `Extension\HeadingPermalink\Slug\DefaultSlugGenerator`   | `Normalizer\SlugNormalizer`          |
| `Extension\HeadingPermalink\Slug\SlugGeneratorInterface` | `Normalizer\TextNormalizerInterface` |

The method signatures of these classes are slightly different:

```php
public function createSlug(string $input): string;
```

To:

```php
public function normalize(string $input, $context = null): string;
```
