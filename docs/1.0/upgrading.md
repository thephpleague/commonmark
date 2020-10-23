---
layout: default
title: Upgrading from 0.19 to 1.0
description: Guide to upgrading to newer versions of this library
redirect_from: /0.20/upgrading/
---

# Upgrading from 0.19 to 1.0

## Previous Deprecations Removed

All previously-deprecated code has been removed. This includes:

- The `safe` option (use `html_input` and `allow_unsafe_links` options instead)
- All deprecated `RegexHelper` constants
- `DocParser::getEnvironment()` (you should obtain it some other way)
- `AbstractInlineContainer` (use `AbstractInline` instead and make `isContainer()` return `true`)

## Document Processors Removed

We no longer support Document Processors because we now have [event dispatching](/1.0/customization/event-dispatcher/) which can fill that same role!  Simply remove the interface from your processor and register it as a listener; for example, instead of this:

```php
class MyDocumentProcessor implements DocumentProcessorInterface
{
    public function processDocument(Document $document)
    {
        // TODO: Do things with the $document
    }
}

// ...

$processor = new MyDocumentProcessor();
$environment->addDocumentProcessor($processor);
```

Simply do this:

```php
class MyDocumentProcessor
{
    public function onDocumentParsed(DocumentParsedEvent $event)
    {
        $document = $event->getDocument();
        // TODO: Do things with the $document
    }
}

// ...

$processor = new MyDocumentProcessor();
$environment->addEventListener(DocumentParsedEvent::class, [$processor, 'onDocumentParsed']);
```

## Text Encoding

This library used to claim it supported ISO-8859-1 encoding but that never truly worked - everything assumed the text was encoded as UTF-8 or ASCII. We've therefore dropped support for ISO-8859-1 and any other unexpected encodings. If you were using some other encoding, you'll now need to convert your Markdown to UTF-8 prior to running it through this library.

Additionally, all public `getEncoding()` or `setEncoding()` methods have been removed, so assume that you're working with UTF-8.

## Inline Processors

The "inline processor" functionality has been removed and replaced with a proper "delimiter processor" feature geared specifically towards dealing with delimiters (which is what the previous implementation tried to do - poorly).

No direct upgrade path exists as this implementation was not widely used, or only used for implementing delimiter processing.  If you fall in the latter category, simply leverage the new functionality instead.  Otherwise, if you have another good use case for inline processors, please let us know in the issue tracker.

## Delimiters

Now that we have proper delimiter handling, we've `final`ized the `Delimiter` class and extracted a `DelimiterInterface` from it.  If you previous extended from `Delimiter` you'll need to implement this new interface instead.

We also deleted these unused `Delimiter` methods:

- `setCanOpen()`
- `setCanClose()`
- `setChar()`
- `setIndex()`
- `setInlineNode()`

And all of the remaining `Delimiter::set___()` methods no longer return `$this`.

## `DocParser`

The `DocParser` class is now `final` as it was never intended to be extended, especially given how so much logic was in `private` methods.  Any custom implementations should implement the new `DocParserInterface` interface instead.

Additionally, the `getEnvironment()` method has been deprecated and excluded from that new interface, as it was only used internally by the `DocParser` and other better ways exist to obtain an environment where needed.

## `Configuration`

The `Configuration` class is now `final` and implements a new `ConfigurationInterface`.  If any of your parsers/renders/etc implement `ConfigurationAwareInterface` you'll need to update that method to accept the new interface instead of the concrete class.

We also renamed/added the following methods:

| Old Name        | New Name    |
|-----------------|-------------|
| `getConfig()`   | `get()`     |
| _n/a_           | `set()`     |
| `setConfig()`   | `replace()` |
| `mergeConfig()` | `merge()`   |

## `AbstractInlineContainer`

The `AbstractInlineContainer` class added an unnecessary level of inheritance and was therefore deprecated. If you previously extended this class, you should now extend from `AbstractInline` and override `isContainer()` to return `true`.

## `AdjoiningTextCollapser`

The `AdjoiningTextCollapser` is an internal class used to combine multiple `Text` elements into one.  If you were using this yourself (unlikely) you'll need to refer to its new name instead: `AdjacentTextMerger`. And if you previously used `collapseTextNodes()` you'll want to switch to using `mergeChildNodes()` instead.

## References

The `ReferenceParser` was moved into the `Reference` sub-namespace, so update your imports accordingly.

Virtually all usages of `ReferenceMap` in type hints have been replaced with the new `ReferenceMapInterface`.  This interface has the same methods, with one small change: `addReference()` no longer returns `$this`.
