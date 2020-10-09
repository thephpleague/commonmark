---
layout: default
title: Upgrading from 1.5 to 2.0 (for developers)
description: Developer guide to upgrading to newer versions of this library
---

## Minimum PHP Version

The minimum supported PHP version was increased from 7.1 to 7.2.

## `CommonMarkConverter` Return Type

In 1.x, calling `convertToHtml()` would return a `string`. In 2.x this changed to return a `RenderedContentInterface`.  To get the resulting HTML, either cast it to a `string` or call `->getContent()`.  (This new interface extends from `Stringable` so you can type hint against that instead, if needed.)

```diff
 use League\CommonMark\CommonMarkConverter;

 $converter = new CommonMarkConverter();
-echo $converter->convertToHtml('# Hello World!');
+echo $converter->convertToHtml('# Hello World!')->getContent();
+// or
+echo (string) $converter->convertToHtml('# Hello World!');
```

## Classes/Namespaces Renamed

Many classes now live in different namespaces, and some have also been renamed.  Here's a quick guide showing their new locations:

_(Note that the base namespace of `League\CommonMark` has been omitted from this table for brevity.)_

| Old Class Namespace/Name (1.x)                                                        | New Class Namespace/Name (2.0)                                                           |
| ------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------- |
| `Environment`                                                                         | `Environment\Environment`                                                                |
| `Extension\CommonMarkCoreExtension`                                                   | `Extension\CommonMark\CommonMarkCoreExtension`                                           |
| `Block\Element\Document`                                                              | `Node\Block\Document`                                                                    |
| `DocParser`                                                                           | `Parser\MarkdownParser`                                                                  |
| `DocParserInterface`                                                                  | `Parser\MarkdownParserInterface`                                                         |
| `HtmlRenderer`                                                                        | `Renderer\HtmlRenderer`                                                                  |
| `ElementRendererInterface`                                                            | `Renderer\HtmlRendererInterface`                                                         |

(This is only a partial list of the clases and interfaces you're likely to work with as a consumer - see the [developer upgrade guide](/2.0/upgrading/developers/) for the complete list.)

## Removed Classes

The following classes have been removed:

| Class name in 1.x              | Replacement / Notes                                                                                           |
| ------------------------------ | ------------------------------------------------------------------------------------------------------------- |
| `Converter`                    | Use `CommonMarkConverter` instead. Note that this has a different constructor but the same methods.           |
| `ConverterInterface`           | Use `MarkdownConverterInterface`.  This interface has the same methods so it should be a drop-in replacement. |

(Several other classes were removed, but these are the only ones you're likely to notice.  See the [developer upgrade guide](/2.0/upgrading/developers/) for the complete list.)

## `EnvironmentInterface::HTML_INPUT_*` constants moved

The following constants have been moved:

| Old Location (1.x)                        | New Location (2.0)   |
| ----------------------------------------- | -------------------- |
| `EnvironmentInterface::HTML_INPUT_ALLOW`  | `HtmlFilter::ALLOW`  |
| `EnvironmentInterface::HTML_INPUT_ESCAPE` | `HtmlFilter::ESCAPE` |
| `EnvironmentInterface::HTML_INPUT_STRIP`  | `HtmlFilter::STRIP`  |

## Renamed Methods

The following methods have been renamed:

| Class                                              | Old Name (1.x)     | New Name (2.0)          |
| -------------------------------------------------- | ------------------ | ----------------------- |
| `Environment` / `ConfigurableEnvironmentInterface` | `addBlockParser()` | `addBlockStartParser()` |
| `ReferenceMap` / `ReferenceMapInterface`           | `addReference()`   | `add()`                 |
| `ReferenceMap` / `ReferenceMapInterface`           | `getReference()`   | `get()`                 |
| `ReferenceMap` / `ReferenceMapInterface`           | `listReferences()` | `getIterator()`         |

## `bin/commonmark` command

This command was buggy to test and was relatively unpopular, so it has been removed. If you need this type of functionality, consider writing your own script with a Converter/Environment configured exactly how you want it.

## `CommonMarkConverter::VERSION` constant

This previously-deprecated constant was removed in 2.0 Use `\Composer\InstalledVersions` provided by composer-runtime-api instead.

## `HeadingPermalinkRenderer::DEFAULT_INNER_CONTENTS` constant

This previously-deprecated constant was removed in 2.0. Use `HeadingPermalinkRenderer::DEFAULT_SYMBOL` instead.

## `heading_permalink/inner_contents` configuration option

This previously-deprecated configuration option was removed in 2.0. Use `heading_permalink/symbol` instead.

## `mentions` configuration options

The `mentions/*/symbol` option has been renamed to `mentions/*/prefix`.

The `mentions/*/regex` option has been renamed to `mentions/*/pattern`.  Additionally, full regular expressions are no longer supported.  Remove the leading/trailing `/` delimiters and any PCRE flags.  For example: `/[\w_]+/iu` should be changed to `[\w_]+`.
