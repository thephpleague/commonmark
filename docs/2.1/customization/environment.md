---
layout: default
title: The Environment
description: Configuring the CommonMark environment with custom options and added functionality
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

echo $converter->convertToHtml('# Hello World!');
```

## addExtension()

```php
public function addExtension(ExtensionInterface $extension);
```

Registers the given [extension](/2.1/customization/extensions/) with the environment.  For example, if you want core CommonMark functionality plus footnote support:

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

Registers the given `BlockStartParserInterface` with the environment with the given priority (a higher number will be executed earlier).

See [Block Parsing](/2.1/customization/block-parsing/) for details.

## addInlineParser()

```php
public function addInlineParser(InlineParserInterface $parser, int $priority = 0);
```

Registers the given `InlineParserInterface` with the environment with the given priority (a higher number will be executed earlier).

See [Inline Parsing](/2.1/customization/inline-parsing/) for details.

## addDelimiterProcessor()

```php
public function addDelimiterProcessor(DelimiterProcessorInterface $processor);
```

Registers the given `DelimiterProcessorInterface` with the environment.

See [Inline Parsing](/2.1/customization/delimiter-processing/) for details.

## addRenderer()

```php
public function addRenderer(string $nodeClass, NodeRendererInterface $renderer, int $priority = 0);
```

Registers a `NodeRendererInterface` to handle a specific type of AST node (`$nodeClass`)  with the given priority (a higher number will be executed earlier).

See [Rendering](/2.1/customization/rendering/) for details.

## addEventListener()

```php
public function addEventListener(string $eventClass, callable $listener, int $priority = 0);
```

Registers the given event listener with the environment.

See [Event Dispatcher](/2.1/customization/event-dispatcher/) for details.

## Priority

Several of these methods allows you to specify a numeric `$priority`.  In cases where multiple things are registered, the internal engine will attempt to use the higher-priority ones first, falling back to lower priority ones if the first one(s) were unable to handle things.

## Accessing the Environment and Configuration within parsers/renderers/etc

If your custom parser/renderer/listener/etc. implements either `EnvironmentAwareInterface` or `ConfigurationAwareInterface` we'll automatically inject the environment or configuration into them once the environment has been fully initialized.  This will provide your code with access to the finalized information it may need.
