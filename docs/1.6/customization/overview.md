---
layout: default
title: Customization Overview
description: An overview of the powerful customization features
---

# Customization Overview

Ready to go beyond the basics of converting Markdown to HTML? This page describes some of the more advanced things you can customize this library to do.

## Parsing and Rendering

The actual process of converting Markdown to HTML has several steps:

 1. Create an [`Environment`](/1.6/customization/environment/), adding whichever extensions/parser/renders you need
 2. Set custom configuration options within the `Environment`
 3. Instantiate a `DocParser` and `HtmlRenderer` using that `Environment`
 4. Use the `DocParser` to parse the Markdown input into an [Abstract Syntax Tree](/1.6/customization/abstract-syntax-tree/) (aka an "AST")
 5. Use the `HtmlRenderer` to convert the [AST `Document`](/1.6/customization/abstract-syntax-tree/#document) into HTML

The `MarkdownConverter` class handles all of this for you, but you can execute that process yourself if you wish:

```php
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;

$environment = Environment::createCommonMarkEnvironment();
$environment->mergeConfig([
    'html_input' => 'strip',
]);

$parser = new DocParser($environment);
$htmlRenderer = new HtmlRenderer($environment);

$markdown = '# Hello World!';

$document = $parser->parse($markdown);
echo $htmlRenderer->renderBlock($document);

// <h1>Hello World!</h1>
```

Feel free to swap out different components or add your own steps in between.  However, the best way to customize this library is to [create your own extensions](/1.6/customization/extensions/) which hook into the parsing and rendering steps - continue reading to see which kinds of extension points are available to you.

## Add Custom Syntax with Parsers

Parsers examine the Markdown input and produce an abstract syntax tree (AST) of the document's structure.
This resulting AST contains both blocks (structural elements like paragraphs, lists, headers, etc) and inlines (words, spaces, links, emphasis, etc).

There are two main types of parsers:

- [Block parsers](/1.6/customization/block-parsing/)
- [Inline parsers](/1.6/customization/inline-parsing/)

The parsing approach is identical for both types - examine text at the current position (via the [`Cursor`](/1.6/customization/cursor/)) and determine if you can handle it;
if so, create the corresponding AST element,
otherwise you abort and the engine will try other parsers.  If no parser succeeds then the current text is treated as plain text.

Simple delimiter-based inlines (like emphasis, strikethrough, etc.) can be parsed without needing a dedicated inline parser by leveraging the new [Delimiter Processing](/1.6/customization/delimiter-processing/) functionality.

## AST manipulation

Once the [Abstract Syntax Tree](/1.6/customization/abstract-syntax-tree/) is parsed, you are free to access/manipulate it as needed before it's passed into the rendering engine.

## Customize HTML Output with Custom Renderers

Renders convert the parsed blocks/inlines from the AST representation into HTML.  There are two types of renderers:

- [Block renderers](/1.6/customization/block-rendering/)
- [Inline renderers](/1.6/customization/inline-rendering/)

When registering these with the environment, you must tell it which block/inline classes it should handle.  This allows you
to essentially "swap out" built-in renderers with your own.

## Examples

Some examples of what's possible:

- [Parse Twitter handles into profile links](/1.6/customization/inline-parsing#example-1---twitter-handles)
- [Convert smilies into emoticon images](/1.6/customization/inline-parsing#example-2---emoticons)
