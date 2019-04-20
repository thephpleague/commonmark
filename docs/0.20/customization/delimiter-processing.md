---
layout: default
title: Delimiter Processing
---

Delimiter Processing
====================

Delimiter processors allow you to implement [delimiter runs](https://spec.commonmark.org/0.29/#delimiter-run) the same way the core library implements emphasis.

These types of inlines are basically text wrapped with one or more characters before **and** after:

~~~markdown
This is an example of **emphasis**. Note how the text is *wrapped* with the same character(s) before and after. 
~~~

Implementing delimiter-based inlines like this require two parts:

 - An [inline parser](/0.20/customization/inline-parsing/)
 - A delimiter processors

## Inline Parsers and the Delimiter Stack

As your [inline parser]() identifies potential delimiter-based inlines, it should create a new `Text` node with the inner contents and also push a new `Delimiter` onto the `DelimiterStack .  For example, here's how our `EmphasisParser` does it:

~~~php
$node = new Text($cursor->getPreviousText(), [
    'delim' => true,
]);
$inlineContext->getContainer()->appendChild($node);

// Add entry to stack to this opener
$delimiter = new Delimiter($character, $numDelims, $node, $canOpen, $canClose);
$inlineContext->getDelimiterStack()->push($delimiter);
~~~

This basically tells the engine that text was found which _might_ be emphasis, but due to the delimiter run rules we can't make that determination just yet.  That final determination is later on by a "delimited processor" - see below.

## Delimiter Processor

A delimiter processor is an instance of `DelimiterProcessorInterface` which uses information from the stack about matching opening/closing delimiters and handles the accordingly.  If a matching pair of openers/closers if found, it removes them from the stack and replaces/wraps the `Text` node with the necessary inline element.

If your custom syntax needs to follow the same rules as CommonMark's emphasis, you'll want to implement `DelimiterProcessorInterface` using the `EmphasisDelimiterProcessor` as a reference.  Once created, be sure to register it with your environment:

~~~php
$environment->addDelimiterProcessor(new MyCustomDelimiterProcessor());
~~~
