---
layout: default
title: Customization Overview
description: An overview of the powerful customization features
---

# Customization Overview

This library uses a three-step process to convert Markdown to HTML:

  1. Parse the various block and inline elements into an Abstract Syntax Tree (AST)
  2. Allow users to iterate and modify the parsed AST
  3. Render the final AST representation to HTML

You can hook into any of these three steps to customize this library to suit your needs.

## Add Custom Syntax with Parsers

Parsers examine the Markdown input and produce an abstract syntax tree (AST) of the document's structure.
This resulting AST contains both blocks (structural elements like paragraphs, lists, headers, etc) and inlines (words, spaces, links, emphasis, etc).

There are two main types of parsers:

- [Block parsers](/1.3/customization/block-parsing/)
- [Inline parsers](/1.3/customization/inline-parsing/)

The parsing approach is identical for both types - examine text at the current position (via the [`Cursor`](/1.3/customization/cursor/)) and determine if you can handle it;
if so, create the corresponding AST element,
otherwise you abort and the engine will try other parsers.  If no parser succeeds then the current text is treated as plain text.

Simple delimiter-based inlines (like emphasis, strikethrough, etc.) can be parsed without needing a dedicated inline parser by leveraging the new [Delimiter Processing](/1.3/customization/delimiter-processing/) functionality.

## AST manipulation

Once the [Abstract Syntax Tree](/1.3/customization/abstract-syntax-tree/) is parsed, you are free to access/manipulate it as needed before it's passed into the rendering engine.

## Customize HTML Output with Custom Renderers

Renders convert the parsed blocks/inlines from the AST representation into HTML.  There are two types of renderers:

- [Block renderers](/1.3/customization/block-rendering/)
- [Inline renderers](/1.3/customization/inline-rendering/)

When registering these with the environment, you must tell it which block/inline classes it should handle.  This allows you
to essentially "swap out" built-in renderers with your own.

## Examples

Some examples of what's possible:

- [Parse Twitter handles into profile links](/1.3/customization/inline-parsing#example-1---twitter-handles)
- [Convert smilies into emoticon images](/1.3/customization/inline-parsing#example-2---emoticons)
