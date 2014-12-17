---
layout: default
title: The Environment
permalink: /customization/environment/
---

The Environment
===============

All parsers, renderers, etc must be registered with the `Environment` class so that the library is aware of them.

A default configuration can be obtained like so:

~~~php
use League\CommonMark;

$environment = Environment::createCommonMarkEnvironment();
~~~

All of the core renders, parsers, etc will be pre-registered and ready to go.

You can customize this default `Environment` (or even a new, empty one) using any of the methods below:

~~~php
public function addBlockParser(BlockParserInterface $parser);

public function addBlockRenderer($blockClass, BlockRendererInterface $blockRenderer);

public function addInlineParser(InlineParserInterface $parser);

public function addInlineProcessor(InlineProcessorInterface $processor);

public function addInlineRenderer($inlineClass, InlineRendererInterface $renderer);
~~~
