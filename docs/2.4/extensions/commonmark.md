---
layout: default
title: CommonMark Core Extension
description: The CommonMarkCoreExtension class includes all core Markdown syntax
---

# CommonMark Core Extension

The `CommonMarkCoreExtension` class contains all of the core Markdown syntax - things like parsing headers, code blocks, links, image, etc.

## Installation

This extension is bundled with `league/commonmark`. This library can be installed via Composer:

```bash
composer require league/commonmark
```

See the [installation](/2.4/installation/) section for more details.

## Included by Default

This extension is automatically installed for you (behind-the-scenes) whenever you instantiate the parser using the `CommonMarkConverter` class:

```php
use League\CommonMark\CommonMarkConverter;

$converter = new CommonMarkConverter();
echo $converter->convert('# Hello World!');
```

## Manual Usage

If you ever create a `new Environment()` from scratch, you'll probably want to include the `CommonMarkCoreExtension()` so you get all the standard Markdown syntax included:

```php
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

// Define your configuration, if needed
$config = [];

// Create a new Environment with the core extension
$environment = new Environment($config);
$environment->addExtension(new CommonMarkCoreExtension());

// Instantiate the converter engine and start converting some Markdown!
$converter = new MarkdownConverter($environment);
echo $converter->convert('# Hello World!');
```

Alternatively, if you don't want all of the core Markdown syntax, avoid using `CommonMarkCoreExtension`.  You can always add just the individual parsers, renderers, etc. you actually want with the [`Environment`](/2.4/customization/environment/).  (This is actually how the [Inlines Only Extension](/2.4/extensions/inlines-only/) works - it only includes a subset of things that `CommonMarkCoreExtension` does!)
