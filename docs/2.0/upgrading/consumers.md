---
layout: default
title: Upgrading from 1.6 to 2.0 (for developers)
description: Developer guide to upgrading to newer versions of this library
---

## Minimum PHP Version

The minimum supported PHP version was increased from 7.1 to 7.4.

## `CommonMarkConverter` and `GithubFlavoredMarkdownConverter` constructors

The constructor methods for both `CommonMarkConverter` and `GithubFlavoredMarkdownConverter` no longer accept passing in a customized `Environment`.  If you want to customize the extensions used in your converter you should switch to using `MarkdownConverter`. See the [Basic Usage](/2.0/basic-usage/) documentation for an example.

```diff
-use League\CommonMark\CommonMarkConverter;
 use League\CommonMark\Environment\Environment;
 use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
 use League\CommonMark\Extension\Table\TableExtension;
+use League\CommonMark\MarkdownConverter;
 
 $config = [
     'html_input' => 'escape',
     'allow_unsafe_links' => false,
     'max_nesting_level' => 100,
 ];
 
 
-$environment = new Environment();
+$environment = new Environment($config);
 $environment->addExtension(new CommonMarkCoreExtension());
 $environment->addExtension(new TableExtension());
 
-$converter = new CommonMarkConverter($config, $environment); // or GithubFlavoredMarkdownConverter
+$converter = new MarkdownConverter($environment);
 
 echo $converter->convertToHtml('Hello World!');
```

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

## HTML Changes

Table of Contents items are no longer wrapped with `<p>` tags:

```diff
 <ul class="table-of-contents">
     <li>
-        <p><a href="#level-2-heading">Level 2 Heading</a></p>
+        <a href="#level-2-heading">Level 2 Heading</a>
     </li>
     <li>
-        <p><a href="#level-4-heading">Level 4 Heading</a></p>
+        <a href="#level-4-heading">Level 4 Heading</a>
     </li>
     <li>
-        <p><a href="#level-3-heading">Level 3 Heading</a></p>
+        <a href="#level-3-heading">Level 3 Heading</a>
     </li>
 </ul>
```

See [#613](https://github.com/thephpleague/commonmark/issues/613) for details.

Additionally, the HTML (including URL fragments) for Heading Permalinks have changed:

```diff
-<h1><a id="user-content-hello-world" href="#hello-world" name="hello-world" class="heading-permalink" aria-hidden="true" title="Permalink">¶</a>Hello World!</h1>
+<h1><a id="content-hello-world" href="#content-hello-world" class="heading-permalink" aria-hidden="true" title="Permalink">¶</a>Hello World!</h1>
```

Note that the `href` now targets the `id` attribute instead of the `name`, which is deprecated in HTML 5.  Additionally, the default prefix has changed to `content`.  See the [Heading Permalink extension documentation](/2.0/extensions/heading-permalinks/#configuration) for more details on how to configure the prefixes.

## Configuration Option Changes

Several configuration options now have new names:

| Old Key/Path                        | New Key/Path                        | Notes                                   |
| ----------------------------------- | ----------------------------------- | --------------------------------------- |
| `enable_em`                         | `commonmark/enable_em`              |                                         |
| `enable_strong`                     | `commonmark/enable_strong`          |                                         |
| `use_asterisk`                      | `commonmark/use_asterisk`           |                                         |
| `use_underscore`                    | `commonmark/use_underscore`         |                                         |
| `unordered_list_markers`            | `commonmark/unordered_list_markers` | Empty arrays no longer allowed          |
| `heading_permalink/id_prefix`       | (unchanged)                         | Default is now `content`                |
| `heading_permalink/inner_contents`  | `heading_permalink/symbol`          |                                         |
| `heading_permalink/slug_normalizer` | `slug_normalizer/instance`          |                                         |
| `max_nesting_level`                 | (unchanged)                         | Only integer values are supported       |
| `mentions/*/symbol`                 | `mentions/*/prefix`                 |                                         |
| `mentions/*/regex`                  | `mentions/*/pattern`                | Cannot contain start/end `/` delimiters |

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
| `ElementRendererInterface`                                                            | `Renderer\ChildNodeRendererInterface`                                                    |
| `HtmlRenderer`                                                                        | `Renderer\HtmlRenderer`                                                                  |

(This is only a partial list of the clases and interfaces you're likely to work with as a consumer – see the [developer upgrade guide](/2.0/upgrading/developers/) for the complete list.)

## Removed Classes

The following classes have been removed:

| Class name in 1.x              | Replacement / Notes                                                                                           |
| ------------------------------ | ------------------------------------------------------------------------------------------------------------- |
| `Converter`                    | Use `MarkdownConverter` instead.                                                                              |
| `ConverterInterface`           | Use `MarkdownConverterInterface`.  This interface has the same methods so it should be a drop-in replacement. |

(Several other classes were removed, but these are the only ones you're likely to notice.  See the [developer upgrade guide](/2.0/upgrading/developers/) for the complete list.)

## Renamed constants

The following constants have been moved/renamed:

| Old Name/Location (1.x)                   | New Name/Location (2.0)  |
| ----------------------------------------- | ------------------------ |
| `EnvironmentInterface::HTML_INPUT_ALLOW`  | `HtmlFilter::ALLOW`      |
| `EnvironmentInterface::HTML_INPUT_ESCAPE` | `HtmlFilter::ESCAPE`     |
| `EnvironmentInterface::HTML_INPUT_STRIP`  | `HtmlFilter::STRIP`      |

## Renamed Methods

The following methods have been renamed:

| Class                                              | Old Name (1.x)     | New Name (2.0)          |
| -------------------------------------------------- | ------------------ | ----------------------- |
| `Environment` / `ConfigurableEnvironmentInterface` | `addBlockParser()` | `addBlockStartParser()` |
| `ReferenceMap` / `ReferenceMapInterface`           | `addReference()`   | `add()`                 |
| `ReferenceMap` / `ReferenceMapInterface`           | `getReference()`   | `get()`                 |
| `ReferenceMap` / `ReferenceMapInterface`           | `listReferences()` | `getIterator()`         |

## Configuration Method Changes

Calling `EnvironmentInterface::getConfig()` without any parameters is no longer supported.

Calling `ConfigurableEnvironmentInterface::mergeConfig()` without any parameters is no longer supported.

The `ConfigurableEnvironmentInterface::setConfig()` method has been removed.  Use `getConfig()` instead.

## `bin/commonmark` command

This command was buggy to test and was relatively unpopular, so it has been removed. If you need this type of functionality, consider writing your own script with a Converter/Environment configured exactly how you want it.

## `CommonMarkConverter::VERSION` constant

This previously-deprecated constant was removed in 2.0. Use `\Composer\InstalledVersions` provided by `composer-runtime-api` instead.

## `HeadingPermalinkRenderer::DEFAULT_INNER_CONTENTS` constant

This previously-deprecated constant was removed in 2.0. Use `HeadingPermalinkRenderer::DEFAULT_SYMBOL` instead.
