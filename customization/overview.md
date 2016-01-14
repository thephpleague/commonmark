---
layout: default
title: Customization Overview
permalink: /customization/overview/
---

Customization Overview
======================

This library supports several types of customizations, which are summarized below.

All examples are also indexed at the bottom of this page.

## New directives via custom parsers

Parsers examine the Markdown input and produce an abstract syntax tree (AST) of the document's structure.
This resulting AST contains both blocks (structural elements like paragraphs, lists, headers, etc) and inlines (words, spaces, links, emphasis, etc).

There are two types of parsers:

- [Block parsers](/customization/block-parsing/)
- [Inline parsers](/customization/inline-parsing/)

The parsing approach is identical for both types - examine text at the current position (via the `Cursor`) and determine if you can handle it;
if so, create the corresponding AST element,
otherwise you abort and the engine will try other parsers.  If no parser succeeds then the current text is treated as plain text. 

## Custom renderers

Renders convert the parsed blocks/inlines from the AST representation into HTML.  There are two types of renderers:

- [Block renderers](/customization/block-rendering/)
- [Inline renderers](/customization/inline-rendering/)

When registering these with the environment, you must tell it which block/inline classes it should handle.  This allows you
to essentially "swap out" built-in renderers with your own.

## AST manipulation

Once the [Abstract Syntax Tree](/customization/abstract-syntax-tree/) is parsed, you are free to access/manipulate it as needed before it's passed into the rendering engine.

## Examples

Some examples of what's possible:

* [Parse Twitter handles into profile links](/customization/inline-parsing#example-1---twitter-handles)
* [Convert smilies into emoticon images](/customization/inline-parsing#example-2---emoticons)
