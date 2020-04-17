---
layout: default
title: Upgrading from 1.3 - 1.4
description: Guide to upgrading to newer versions of this library
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
