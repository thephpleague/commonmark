---
layout: default
title: The Environment
description: Configuring the CommonMark environment with custom options and added functionality
---

# The Environment

The `Environment` contains all of the parsers, renderers, configurations, etc. that the library uses during the conversion process.  You therefore must register all parsers, renderers, etc. with the `Environment` so that the library is aware of them.

A pre-configured `Environment` can be obtained like this:

```php
use League\CommonMark\Environment;

$environment = Environment::createCommonMarkEnvironment();
```

All of the core renders, parsers, etc. needed to implement the CommonMark spec will be pre-registered and ready to go.

You can customize this default `Environment` (or even a new, empty one) using any of the methods below (from the `ConfigurableEnvironmentInterface` interface).

## mergeConfig()

```php
public function mergeConfig(array $config = []);
```

Merges the given [configuration](/1.4/configuration/) settings into any existing ones.

## setConfig()

```php
public function setConfig(array $config = []);
```

Completely replaces the previous [configuration](/1.4/configuration/) settings with the new `$config` you provide.

## addExtension()

```php
public function addExtension(ExtensionInterface $extension);
```

Registers the given [extension](/1.4/customization/extensions/) with the environment.  This is typically how you'd integrate third-party extensions with this library.

## addBlockParser()

```php
public function addBlockParser(BlockParserInterface $parser, int $priority = 0);
```

Registers the given `BlockParserInterface` with the environment with the given priority (a higher number will be executed earlier).

See [Block Parsing](/1.4/customization/block-parsing/) for details.

## addBlockRenderer()

```php
public function addBlockRenderer(string $blockClass, BlockRendererInterface $blockRenderer, int $priority = 0);
```

Registers a `BlockRendererInterface` to handle a specific type of block (`$blockClass`)  with the given priority (a higher number will be executed earlier).

See [Block Rendering](/1.4/customization/block-rendering/) for details.

## addInlineParser()

```php
public function addInlineParser(InlineParserInterface $parser, int $priority = 0);
```

Registers the given `InlineParserInterface` with the environment with the given priority (a higher number will be executed earlier).

See [Inline Parsing](/1.4/customization/inline-parsing/) for details.

## addInlineRenderer()

```php
public function addInlineRenderer(string $inlineClass, InlineRendererInterface $renderer, int $priority = 0);
```

Registers an `InlineRendererInterface` to handle a specific type of inline (`$inlineClass`) with the given priority (a higher number will be executed earlier).
A single renderer can handle multiple inline classes, but you must register it separately for each type. (The same renderer instance can be re-used if desired.)

See [Inline Rendering](/1.4/customization/inline-rendering/) for details.

## addDelimiterProcessor()

```php
public function addDelimiterProcessor(DelimiterProcessorInterface $processor);
```

Registers the given `DelimiterProcessorInterface` with the environment.

See [Inline Parsing](/1.4/customization/delimiter-processing/) for details.

## addEventListener()

```php
public function addEventListener(string $eventClass, callable $listener, int $priority = 0);
```

Registers the given event listener with the environment.

See [Event Dispatcher](/1.4/customization/event-dispatcher/) for details.

## Priority

Several of these methods allows you to specify a numeric `$priority`.  In cases where multiple things are registered, the internal engine will attempt to use the higher-priority ones first, falling back to lower priority ones if the first one(s) were unable to handle things.
