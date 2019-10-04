---
layout: default
title: Upgrading from 0.18 to 0.19
---

# Upgrading from 0.18 to 0.19

The `Environment` and extension framework underwent some major changes in this release.  Please read this if you maintain any community extensions or have written custom functionality on top of this library.

## PHP support

This library no longer supports PHP 5.6 or 7.0.  Feel free to remove support for those from your extensions as well.

## HTML attribute escaping

Previously, any attributes passed into an `HtmlElement` would need to be pre-escaped. This is now done for you so be sure to remove any references to `Xml::escape()` when applied to attributes.

This does not affect inner contents which may still need pre-escaping of untrusted user input.

## Removed classes and interface methods

The `getName()` method has been removed from several classes:

 - `BlockParserInterface` and `AbstractBlockParser`
 - `InlineParserInterface` and `AbstractInlineParser`

This method was originally intended for supporting XML rendering, which was never implemented, and will likely define names a bit differently if/when we do add support.

After doing this, the two abstract classes mentioned above had nothing left in them, so those were removed.  Any parsers previously extending them should directly implement the corresponding interface instead.

`InlineContainer` was also removed.

`Xml::escape()` no longer accepts the deprecated `$preserveEntities` parameter.

## Removed deprecated `RegexHelper` methods

Several previously-deprecated methods inside of `RegexHelper` were finally removed.  That functionality was made available with static methods and constants, so use those instead.

## Parameter and return types

Pretty much every method now uses parameter and return types, including several interfaces.  Update your implementations accordingly.

## Environment interfaces

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

Finally, please note that the `getStrings()` method does no longer exists on the `AbstractBlock` class.  If you previously relied on this, consider making these changes to your block element class:

1. Extend from `AbstractStringContainerBlock` instead of `AbstractBlock`
2. Override its `finalize()` method to set the final contents like so: `$this->finalStringContents = implode("\n", $this->strings);`
3. Call the `getStringContent()` method wherever you need to obtain those finalized string contents

## Extensions

Extensions work much differently now.  In the past, you'd have functions returning an array of things that the `Environment` would register for you.

The `ExtensionInterface` was changed to have a single `register(ConfigurableEnvironmentInterface $environment)` method.  You must now manually `add()` all your parsers, processors, and renderers yourself directly within the environment you are provided.  See the changes made to `CommonMarkCoreExtension` for a good example.

The `Environment` will still automatically inject the `Environment` or `Configuration` for any parsers, processors, and renderers implementing the `EnvironmentAwareInterface` or `ConfigurationAwareInterface` - that behavior hasn't changed.

## Adding renderers with short names

`Environment::add___Renderer()` now requires the fully-qualified class name with namespace as its first argument.  Providing just the class name without the namespace will no longer work.  

## Prioritization of parsers, processors, and renderers

The execution order of these things no longer depends on the order you add them - you can now specific custom priorities when `add()`ing them to the `Environment`!  The priority can be any integer you want.  The default value is `0`. All CommonMark Core things will have a priority between -255 and 255.  The higher the number, the earlier it will be executed.

## Multiple block/inline renderers per class

Thanks to the new prioritization system, we now support multiple renderers for the same block/inline class!  The first renderer to return a non-null result will be considered the "winner" and no subsequent renderers will execute for that block/inline.  No change should be required for most extensions unless you were using some weird workaround to support multiple renderers yourself. 

## `RegexHelper::isEscapable()` no longer accepts `null` values

In cases where you may have previously passed a `null` value in, skip the call to this method.  The previous behavior was to return `false` for `null` values, but `null` is never escapable so it's silly to make this call when we know what the result will be.
