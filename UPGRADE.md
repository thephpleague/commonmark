# Upgrade Instructions

**Note:** This file has been deprecated.  Future upgrade instructions can be found on our website: <https://commonmark.thephpleague.com/releases>

## UNRELEASED

## 0.19

The `Environment` and extension framework underwent some major changes in this release.

### PHP support

This library no longer supports PHP 5.6 or 7.0.  Feel free to remove support for those from your extensions as well.

### HTML attribute escaping

Previously, any attributes passed into an `HtmlElement` would need to be pre-escaped. This is now done for you so be sure to remove any references to `Xml::escape()` when applied to attributes.

This does not affect inner contents which may still need pre-escaping of untrusted user input.

### Removed classes and interface methods

The `getName()` method has been removed from several classes:

 - `BlockParserInterface` and `AbstractBlockParser`
 - `InlineParserInterface` and `AbstractInlineParser`

This method was originally intended for supporting XML rendering, which was never implemented, and will likely define names a bit differently if/when we do add support.

After doing this, the two abstract classes mentioned above had notthing left in them, so those were removed.  Any parsers previously extending them should directly implement the corresponding interface instead.

`InlineContainer` was also removed.

`Xml::escape()` no longer accepts the deprecated `$preserveEntities` parameter.

### Removed deprecated `RegexHelper` methods

Several previously-deprecated methods inside of `RegexHelper` were finally removed.  That functionality was made available with static methods and constants, so use those instead.

### Parameter and return types

Pretty much every method now uses parameter and return types, including several interfaces.  Update your implementations accordingly.

### Environment interfaces

We have extracted two interfaces from the `Environment` class:

 - `EnvironmentInterface` - contains all the getters; use this in your parsers, renderers, etc.
 - `ConfigurableEnvironmentInterface` - contains all the `add` methods, as well as `setConfig()` and `mergeConfig`

As a result, `EnvironmentAwareInterface` now requires an `EnvironmentInterface` instead of an `Environment`, so update your parsers/processors/renderers accordingly.

### Block Elements

A few methods from `AbstractBlock` have been extracted into a new `AbstractStringContainerBlock` class and corresponding `StringContainerInterface` interface:

 - `addLine(string $line)`
 - `getStringContent()`
 - `handleRemainingContents(ContextInterface $context, Cursor $cursor)`

These are used to represent a block which can contain strings of text inside (even if those strings do not contain "inline" elements but just plain text).

To determine how to best upgrade your existing block element classes, look at the value returned by the `acceptsLines()` method:

 - If `acceptsLines()` returns `false`, simply remove the three methods from the bulleted list above, along with `acceptsLines()` and any calls to `parent::__construct()`.
 - If `acceptsLines()` returns `true`, change your base class from `AbstractBlock` to `AbstractStringContainerBlock` and remove `acceptsLines()`.

Additionally, `StringContainerInterface` now extends this new `StringContainerInterface` interface. Just make sure you've implemented the change mentioned above and you should be fine.

### Extensions

Extensions work much differently now.  In the past, you'd have functions returning an array of things that the `Environment` would register for you.

The `ExtensionInterface` was changed to have a single `register(ConfigurableEnvironmentInterface $environment)` method.  You must now manually `add()` all your parsers, processors, and renderers yourself directly within the environment you are provided.  See the changes made to `CommonMarkCoreExtension` for a good example.

The `Environment` will still automatically inject the `Environment` or `Configuration` for any parsers, processors, and renderers implementing the `EnvironmentAwareInterface` or `ConfigurationAwareInterface` - that behavior hasn't changed.

### Adding renderers with short names

`Environment::add___Renderer()` now requires the fully-qualified class name with namespace as its first argument.  Providing just the class name without the namespace will no longer work.  

### Prioritization of parsers, processors, and renderers

The execution order of these things no longer depends on the order you add them - you can now specific custom priorities when `add()`ing them to the `Environment`!  The priority can be any integer you want.  The default value is `0`. All CommonMark Core things will have a priority between -255 and 255.  The higher the number, the earlier it will be executed.

### Multiple block/inline renderers per class

Thanks to the new prioritization system, we now support multiple renderers for the same block/inline class!  The first renderer to return a non-null result will be considered the "winner" and no subsequent renderers will execute for that block/inline.  No change should be required for most extensions unless you were using some weird workaround to support multiple renderers yourself. 

### `RegexHelper::isEscapable()` no longer accepts `null` values

In cases where you may have previously passed a `null` value in, skip the call to this method.  The previous behavior was to return `false` for `null` values, but `null` is never escapable so it's silly to make this call when we know what the result will be.

## 0.18.3

### Deprecated `Xml::escape()` argument

Starting in `0.19.0`, the `Xml::escape()` function will no longer accept the second `$preserveEntities` argument as this can lead to XSS issues.  Remove this argument if your code uses it.  See https://github.com/thephpleague/commonmark/issues/353 for further details.

## 0.18.0

No breaking changes were introduced, but we did add a new interface: `ConverterInface`. Consider depending on this interface in your code instead of the concrete implementation. (See #330)

## 0.17.0

## Minimum PHP version

The minimum PHP version has been increased to 5.6.5.  Users on PHP 5.4 and 5.5 can still use previous versions of this library but will not receive future improvements or bug fixes.

## Removal of deprecated features

Pretty much everything marked as `@deprecated` in 0.16.0 has been removed.

## `RegexHelper`

We're now taking advantage of PHP 5.6's constant expression feature. This removes the need for `RegexHelper` to be a singleton where complex regular expressions are built and referenced using instance methods.  **All regexes are now available as class constants.**

For example, instead of doing this:

```php
preg_match('/' . RegexHelper::getInstance()->getPartialRegex(RegexHelper::OPENTAG) . '/', $html);
```

You can now do this:

```php
preg_match('/' . RegexHelper::PARTIAL_OPENTAG . '/', $html);
```

(Basically, remove that function call and prefix the constant name with `PARTIAL_`).

Other instance functions like `getLinkTitleRegex()` which returned a regular expression have also been deprecated in favor of pre-defined constants like `PARTIAL_LINK_TITLE`.

The now-deprecated functionality still exists in 0.17.0 **but will be removed in the next major release.**

To summarize:

 - All `REGEX_` constants are fully-formed regexes. Most are unchanged.
 - All `PARTIAL_` constants need to be wrapped with a `/` on each side before use.
 - All instance methods are deprecated - use a constant instead.

`RegexHelper` is also `final` now - it only contains constants and static methods and was never intended to be extended.

## Cursor state

`Cursor::saveState()` and `Cursor::restoreState()` provide the ability to rollback the state of a `Cursor`. For example:

```php
$oldState = $cursor->saveState();

// Made-up example of trying to parse something using calls
$cursor->advanceToNextNonSpaceOrTab();
$cursor->match('/foo(bar)?/');
$cursor->advanceToNextNonSpaceOrTab();

if ($someConditionThatWeDidntExpect) {
    // Roll back and abort
    $cursor->restoreState($oldState);
    return;
}
```

This useful feature encapsulated the internal, `private` state of the `Cursor` inside of a `CursorState` object with public methods.  **This was a design mistake** as it meant that any changes to the interal structure of a `Cursor` meant causing BC-breaks on the `CursorState`.

`CursorState` was also never intended for any other usage besides saving/restoring.

For those reasons, we've removed the `CursorState` class entirely and now store the state using an array. **Do not depend on the contents or structure of the array for any reason as it may change in any release without warning!**  If you really need to reference information about the prior state of the cursor, either `clone` it or grab the info you need before manipulating it.

## `InlineContainer` interface

The `InlineContainer` interface was renamed to `InlineContainerInterface`.  The old one still exists as a deprecated interface and will be removed in the next major release.

## 0.16.0

You may continue using the deprecated items listed below in version 0.16.x.  **However, these deprecations will be removed in a future major release** (0.17.0+ or 1.0.0, whichever comes first) so consider updating your code now to prepare for that release.

## `Cursor` and `CursorState` methods

Basically, all methods in these two classes which contain `First` in their name have been deprecated.  The original names were misleading as they always operated on the "first" non-space **after the current position**, which is not always the **first occurrence in the string**. You should instead use the `Next` versions instead:

 - Deprecated `Cursor::advanceWhileMatches()`
   - Use `Cursor::match()` instead.
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

`RegexHelper::REGEX_UNICODE_WHITESPACE` and `RegexHelper::getLinkDestinationRegex()` were no longer needed as of the 0.15.5 release and have therefore been deprecated and marked for removal.

### `HtmlRenderer::escape()`

`HtmlRenderer::escape()` was an instance method making it unusable as a general utility method.  Its logic has been moved into a new static Xml::escape() method so use that instead - it takes the same exact methods and implements the same behavior.

### Final Utility Classes

The following utility classes were never meant to be extended and have therefore been marked `final`:

 - `Html5Entities`
 - `LinkParserHelper`
 - `UrlEncoder`

## 0.15.0

### `CursorState` constructor

The `CursorState` constructor now requires an additional boolean parameter `$partiallyConsumedTab`.
No change should be needed in your application unless you are directly instantiating this object (unlikely).

### `DelimiterStack::findFirstMatchingOpener()` deprecated

You should use `DelimiterStack::findMatchingOpener()` instead.

The method signature is almost identical, except for the inclusion of a by-reference boolean `$oddMatch`.

The deprecated `findFirstMatchingOpener()` method was removed in the 0.16.0 release.

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

