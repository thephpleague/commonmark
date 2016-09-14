# Upgrade Instructions

## 0.15.0

### `CursorState` constructor

The `CursorState` constructor now requires an additional boolean parameter `$partiallyConsumedTab`.
No change should be needed in your application unless you are directly instantiating this object (unlikely).

### `DelimiterStack::findFirstMatchingOpener()` deprecated

You should use `DelimiterStack::findMatchingOpener()` instead.

The method signature is almost identical, except for the inclusion of a by-reference boolean `$oddMatch`.

The deprecated `findFirstMatchingOpener()` method may be removed as early as 0.16.0 or 1.0.0.

## 0.14.0

### `safe` option deprecated

The `safe` option has been deprecated and replaced with two new configuration options:

* `html_input` - How to handle HTML input.  Set this option to one of the following values:
  - `strip` - Strip all HTML (equivalent to `'safe' => true`)
  - `allow` - Allow all HTML input as-is (equivalent to `'safe' => false)
  - `escape` - Escape all HTML

* `allow_unsafe_links` - Whether to allow risky image URLs and links
  - `true` - Allow (equivalent to `'safe' => false`)
  - `false` - Remove all risky URLs (equivalent to `'safe' => true`)

Although `safe` will continue to work until 1.0.0 you should consider updating your configuration now if possible.

