# Upgrade Instructions

## 0.16.0 (unreleased)

You may continue using the deprecated items listed below in version 0.16.x.  **However, these deprecations will be removed in a future major release** (0.17.0+ or 1.0.0, whichever comes first) so consider updating your code now to prepare for that release.

## `Cursor` and `CursorState` methods

Basically, all methods in these two classes which contain `First` in their name have been deprecated.  The original names were misleading as they always operated on the "first" non-space **after the current position**, which is not always the **first occurrence in the string**. You should instead use the `Next` versions instead:

 - Deprecated `CursorState::getFirstNonSpaceCache()`
   - Use `CursorState::getNextNonSpaceCache()` instead (identical behavior)
 - Deprecated `Cursor::getFirstNonSpaceCharacter()`
   - Use `Cursor::getNextNonSpaceCharacter()` instead (identical behavior)
 - Deprecated `Cursor::getFirstNonSpacePosition()`
   - Use `Cursor::getNextNonSpacePosition()` instead (identical behavior)
 - Deprecated `Cursor::advanceToFirstNonSpace()`
   - You'll probably want to use `advanceToNextNonSpaceOrTab()` if you're using this to parse blocks, but beware that it does not behave identically to the original method.
   - If you need the exact functionality as the original, use `advanceToNextNonSpaceOrNewline()` instead.  We're currently using this internally for parsing links and references.

The reason we now have two alternatives to the `advancedToFirstNonSpace()` function is because we accidentally assumed that a single approach would work in two different use cases.  As you can see in [issue #279](https://github.com/thephpleague/commonmark/issues/279), this assumption was false.  We have therefore split the two different parsing strategies into two different methods.  Both will behave similarly for strings that only contain spaces, but they differ when newlines or tabs are involved.

More details about this change can be found here: https://github.com/thephpleague/commonmark/issues/280

### `RegexHelper`

`RegexHelper::REGEX_UNICODE_WHITESPACE` and `RegexHelper::getLinkDestinationRegex()` were no longer needed as of the 0.15.5 release and have therefore been deprecated.  They will be removed in 0.17.0 or 1.0.0 (whichever comes first).

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

