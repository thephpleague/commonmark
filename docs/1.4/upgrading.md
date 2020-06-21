---
layout: default
title: Upgrading from 1.3 - 1.4
description: Guide to upgrading to newer versions of this library
redirect_from: /upgrading/
---

# Upgrading from 1.3 to 1.4

## Changes

### Rendering block/inline subclasses

Imagine you have the following inline elements:

```php
class Link extends AbstractWebResource { } // This is the is the standard CommonMark "Link" element

class ShortLink extends Link { } // A custom inline node type you created which extends "Link"

class BitlyLink extends ShortLink { } // Another custom inline node type you created
```

Prior to 1.4, you'd have to manually register corresponding inline renderers for each one:

```php
/** @var \League\CommonMark\Environment $environment */
$environment->addInlineRenderer(Link::class, new LinkRenderer()); // this line is usually automatically done for you
$environment->addInlineRenderer(ShortLink::class, new LinkRenderer()); // register for custom node type; required before 1.4
$environment->addInlineRenderer(BitlyLink::class, new LinkRenderer()); // register for custom node type; required before 1.4
```

But in 1.4 onwards, you no longer need to manually register that `LinkRenderer` for subclasses (like `ShortLink` and `BitlyLink` in the example above) - if the `Environment` can't find a registered renderer for that specific block/inline node type, we'll automatically check if the node's parent classes have a registered renderer and use that instead.

Previously, if you forgot to register those renderers, the rendering process would fail with a `RuntimeException` like "Unable to find corresponding renderer".

## `ListBlock::TYPE_` constant values

The two constants in the `ListBlock` class no longer contain title-cased values - the first character is now lowercased.  Ideally, you should be referencing the constants, but if you were instead hard-coding these values in your application, you may need to adjust those hard-coded strings.

## Deprecations

Several things have been deprecated in 1.4 - they'll continue to work, but consider using alternatives to make your code easier to upgrade to 2.0 when these deprecated things are removed.

### `ListBlock::TYPE_UNORDERED` constant

The `ListBlock::TYPE_UNORDERED` constant has been deprecated, use `ListBlock::TYPE_BULLET` instead.

### `bin/commonmark` command

This command has been buggy to test and is relatively unpopular, so this will be removed in 2.0. If you need this type of functionality, consider writing your own script with a Converter/Environment configured exactly how you want it.

### `ArrayCollection` methods

This class has several unused methods, or methods with an existing alternative:

| Method Name         | Alternative                                          |
| ------------------- | ---------------------------------------------------- |
| `add($value)`       | `$collection[] = $value`                             |
| `set($key, $value)` | `$collection[$key] = $value`                         |
| `get($key)`         | `$collection[$key]`                                  |
| `remove($key)`      | `unset($collection[$key])`                           |
| `isEmpty()`         | `count($collection) === 0`                           |
| `contains($value)`  | `in_array($value, $collection->toArray(), true)`     |
| `indexOf($value)`   | `array_search($value, $collection->toArray(), true)` |
| `containsKey($key)` | `isset($collection[$key])`                           |
| `replaceWith()`     | (none provided)                                      |
| `removeGaps()`      | (none provided)                                      |

### `Converter` and `ConverterInterface`

The `Converter` class has been deprecated - switch to using `CommonMarkConverter` instead.

The `ConverterInterface` has been deprecated - switch to using `MarkdownConverterInterface` instead.

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
