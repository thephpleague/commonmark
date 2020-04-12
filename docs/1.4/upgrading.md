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

### `Converter` constructor

Instantiating the `Converter` by passing a `DocParserInterface` and `ElementRendererInterface` into the constructor is deprecated. You can keep doing this for now, but in 2.0 we'll be changing the constructor instead accept a configuration array and `EnvironmentInterface`, just like `CommonMarkConverter` does today.
