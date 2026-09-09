---
layout: default
title: The Environment
description: Configuring the CommonMark environment with custom options and added functionality
redirect_from:
  - /customization/environment/
  - /2.0/customization/environment/
  - /2.1/customization/environment/
  - /2.2/customization/environment/
  - /2.3/customization/environment/
  - /2.4/customization/environment/
  - /2.5/customization/environment/
  - /2.6/customization/environment/
  - /2.7/customization/environment/
---

# The Environment

The `Environment` contains all of the parsers, renderers, configurations, etc. that the library uses during the conversion process.  You therefore must register all extensions, parsers, renderers, etc. with the `Environment` so that the library is aware of them.

An empty `Environment` can be obtained like this:

```php
use League\CommonMark\Environment\Environment;

$config = [];
$environment = new Environment($config);
```

You can customize the `Environment` using any of the methods below (from the `EnvironmentBuilderInterface` interface).

Once your `Environment` is configured with whatever configuration and extensions you want, you can instantiate a `MarkdownConverter` and start converting MD to HTML:

```php
use League\CommonMark\MarkdownConverter;

// Using $environment from the previous code sample
$converter = new MarkdownConverter($environment);

echo $converter->convert('# Hello World!');
```

## addExtension()

```php
public function addExtension(ExtensionInterface $extension);
```

Registers the given [extension](/2.x/customization/extensions/) with the environment.  For example, if you want core CommonMark functionality plus footnote support:

```php
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Footnote\FootnoteExtension;

$config = [];
$environment = new Environment($config);

$environment->addExtension(new CommonMarkCoreExtension());
$environment->addExtension(new FootnoteExtension());
```

## addBlockStartParser()

```php
public function addBlockStartParser(BlockStartParserInterface $parser, int $priority = 0);
```

Registers the given `BlockStartParserInterface` with the environment with the given [priority](#priority).

See [Block Parsing](/2.x/customization/block-parsing/) for details.

## addInlineParser()

```php
public function addInlineParser(InlineParserInterface $parser, int $priority = 0);
```

Registers the given `InlineParserInterface` with the environment with the given [priority](#priority).

See [Inline Parsing](/2.x/customization/inline-parsing/) for details.

## addDelimiterProcessor()

```php
public function addDelimiterProcessor(DelimiterProcessorInterface $processor);
```

Registers the given `DelimiterProcessorInterface` with the environment.

See [Inline Parsing](/2.x/customization/delimiter-processing/) for details.

## addRenderer()

```php
public function addRenderer(string $nodeClass, NodeRendererInterface $renderer, int $priority = 0);
```

Registers a `NodeRendererInterface` to handle a specific type of AST node (`$nodeClass`) with the given [priority](#priority).

See [Rendering](/2.x/customization/rendering/) for details.

## addEventListener()

```php
public function addEventListener(string $eventClass, callable $listener, int $priority = 0);
```

Registers the given event listener with the environment.

See [Event Dispatcher](/2.x/customization/event-dispatcher/) for details.

## Priority

Several of these methods allow you to specify a numeric `$priority`. Higher-priority components are attempted first, with lower-priority ones used as fallbacks when appropriate.

**If execution order matters, always set an explicit priority.** Components sharing the same priority have no guaranteed order relative to each other, so don't rely on the order you registered them in. Choose the number relative to the component you need to outrank rather than to `0` - although `$priority` defaults to `0`, the built-in extensions span a wide range:

- **Block start parsers** - `CommonMarkCoreExtension` uses `70` down to `-100`. Note the additional `250` threshold described in [Block Parsing](/2.x/customization/block-parsing/).
- **Inline parsers** - `CommonMarkCoreExtension` uses `200` down to `10`.
- **Renderers** - all core renderers are registered at `0`, so a priority of `1` is enough to take precedence.

Registration order is especially unreliable when mixing direct calls with extensions: extensions don't register their components until the environment is first used, so anything added directly jumps ahead of them. This is why a custom renderer works when added via `addRenderer()` but silently does nothing when registered inside an extension. An explicit priority avoids the problem - including for [event listeners](/2.x/customization/event-dispatcher/), whose documented same-priority ordering is subject to the same deferral.

## Accessing the Environment and Configuration within parsers/renderers/etc

If your custom parser/renderer/listener/etc. implements either `EnvironmentAwareInterface` or `ConfigurationAwareInterface` we'll automatically inject the environment or configuration into them once the environment has been fully initialized.  This will provide your code with access to the finalized information it may need.
