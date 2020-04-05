---
layout: default
title: The Environment
---

The Environment
===============

All parsers, renderers, etc. must be registered with the `Environment` class so that the library is aware of them.

A pre-configured `Environment` can be obtained like this:

~~~php
<?php

use League\CommonMark;

$environment = Environment::createCommonMarkEnvironment();
~~~

All of the core renders, parsers, etc. will be pre-registered and ready to go.

You can customize this default `Environment` (or even a new, empty one) using any of the methods below (from the `ConfigurableEnvironmentInterface` interface).
(These are the same methods used by `Environment::createCommonMarkEnvironment()` to register the standard functionality.)

## addBlockParser()

~~~php
<?php

public function addBlockParser(BlockParserInterface $parser, int $priority = 0);
~~~

Registers the given `BlockParserInterface` with the environment with the given priority (a higher number will be executed earlier).

See [Block Parsing](/0.19/customization/block-parsing/) for details.

## addBlockRenderer()

~~~php
<?php

public function addBlockRenderer(string $blockClass, BlockRendererInterface $blockRenderer, int $priority = 0);
~~~

Registers a `BlockRendererInterface` to handle a specific type of block (`$blockClass`)  with the given priority (a higher number will be executed earlier).

See [Block Rendering](/0.19/customization/block-rendering/) for details.

## addInlineParser()

~~~php
<?php

public function addInlineParser(InlineParserInterface $parser, int $priority = 0);
~~~

Registers the given `InlineParserInterface` with the environment with the given priority (a higher number will be executed earlier).

See [Inline Parsing](/0.19/customization/inline-parsing/) for details.

## addInlineProcessor()

~~~php
<?php

public function addInlineProcessor(InlineProcessorInterface $processor);
~~~

Registers the given `InlineProcessorInterface` with the environment.

**TODO:** Add documentation for this.

## addInlineRenderer()

~~~php
<?php

public function addInlineRenderer(string $inlineClass, InlineRendererInterface $renderer, int $priority = 0);
~~~

Registers an `InlineRendererInterface` to handle a specific type of inline (`$inlineClass`) with the given priority (a higher number will be executed earlier).
A single renderer can handle multiple inline classes, but you must register it separately for each type. (The same renderer instance can be re-used if desired.)

See [Inline Rendering](/0.19/customization/inline-rendering/) for details.

## addDocumentProcessor()

~~~php
<?php

public function addDocumentProcessor(DocumentProcessorInterface $processor, int $priority = 0);
~~~

Adds a new Document Processor which will [manipulate the AST](/0.19/customization/abstract-syntax-tree/) after parsing the document but before rendering it.

## Priority

Each of these methods allows you to specify a numeric `$priority`.  In cases where multiple things are registered, the internal engine will attempt to use the higher-priority ones first, falling back to lower priority ones if the first one(s) were unable to handle things.
