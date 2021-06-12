---
layout: default
title: Upgrading from 1.4 - 1.5
description: Guide to upgrading to newer versions of this library
---

# Upgrading from 1.4 to 1.5

## Changes

`Reference` labels are no longer auto-normalized within the `Reference` constructor. Normalization only occurs within the `ReferenceMap`.

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

### `heading_permalink/inner_contents` configuration option

Prior to `1.5.0`, this configuration option's default value was an embedded [Octicon link SVG](https://iconify.design/icon-sets/octicon/link.html),
but any custom HTML could be provided.

If you wish to restore the previous functionality, you may supply `inner_contents` with the original default value by
using the constant `HeadingPermalinkRenderer::DEFAULT_INNER_CONTENTS` (which is now also deprecated):

```php
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkRenderer;

$config = [
    'heading_permalink' => [
        'inner_contents' => HeadingPermalinkRenderer::DEFAULT_INNER_CONTENTS,
    ],
];
```

This configuration option will be removed in `2.0.0` in favor of the new `heading_permalink/symbol` configuration
option. Moving forward, you will need to supply your own custom icon via CSS by removing the default `symbol` value:

```php
$config = [
    'heading_permalink' => [
        'html_class' => 'heading-permalink',
        'symbol' => '',
    ],
];
```

Then targeting the `html_class` given in the configuration in your CSS:

```css
/**
 * Custom SVG Icon.
 */
.heading-permalink::after {
  display: inline-block;
  content: "";
  /**
   * Octicon Link (https://iconify.design/icon-sets/octicon/link.html)
   * [Pro Tip] Use an SVG URL encoder (https://yoksel.github.io/url-encoder).
   */
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' aria-hidden='true' style='-ms-transform:rotate(360deg);-webkit-transform:rotate(360deg)' viewBox='0 0 16 16' transform='rotate(360)'%3E%3Cpath fill-rule='evenodd' d='M4 9h1v1H4c-1.5 0-3-1.69-3-3.5S2.55 3 4 3h4c1.45 0 3 1.69 3 3.5 0 1.41-.91 2.72-2 3.25V8.59c.58-.45 1-1.27 1-2.09C10 5.22 8.98 4 8 4H4c-.98 0-2 1.22-2 2.5S3 9 4 9zm9-3h-1v1h1c1 0 2 1.22 2 2.5S13.98 12 13 12H9c-.98 0-2-1.22-2-2.5 0-.83.42-1.64 1-2.09V6.25c-1.09.53-2 1.84-2 3.25C6 11.31 7.55 13 9 13h4c1.45 0 3-1.69 3-3.5S14.5 6 13 6z' fill='%23626262'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-size: 1em;
}
```
