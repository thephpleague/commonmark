---
layout: default
title: The Environment
permalink: /customization/environment/
---

The Environment
===============

All parsers, renderers, etc. must be registered with the `Environment` class so that the library is aware of them.

A pre-configured `Environment` can be obtained like this:

~~~php
use League\CommonMark;

$environment = Environment::createCommonMarkEnvironment();
~~~

All of the core renders, parsers, etc. will be pre-registered and ready to go.

You can customize this default `Environment` (or even a new, empty one) using any of the methods below:

~~~php
public function addBlockParser(BlockParserInterface $parser);

public function addBlockRenderer($blockClass, BlockRendererInterface $blockRenderer);

public function addInlineParser(InlineParserInterface $parser);

public function addInlineProcessor(InlineProcessorInterface $processor);

public function addInlineRenderer($inlineClass, InlineRendererInterface $renderer);
~~~

These are the same methods used by `Environment::createCommonMarkEnvironment()` to register the standard functionality.
