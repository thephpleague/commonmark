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

See the [installation](/2.0/installation/) section for more details.

## Included by Default

This extension is automatically installed for you (behind-the-scenes) whenever you instantiate the parser using the `CommonMarkConverter` class:

```php
use League\CommonMark\CommonMarkConverter;

$converter = new CommonMarkConverter();
echo $converter->convertToHtml('# Hello World!');
```

Or if you call the `Environment::createCommonMarkEnvironment()` helper:

```php
use League\CommonMark\Parser\MarkdownParser;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Renderer\HtmlRenderer;

$environment = Environment::createCommonMarkEnvironment();

$parser = new MarkdownParser($environment);
$htmlRenderer = new HtmlRenderer($environment);

$markdown = '# Hello World!';

$document = $parser->parse($markdown);
echo $htmlRenderer->renderDocument($document);
```

## Manual Usage

If you ever create a `new Environment()` from scratch, you'll probably want to include the `CommonMarkCoreExtension()` so you get all the standard Markdown syntax included:

```php
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

$environment = new Environment();
$environment->addExtension(new CommonMarkCoreExtension());

// Set your configuration if needed
$environment->mergeConfig([
    // ...
]);

// Instantiate the converter engine and start converting some Markdown!
$converter = new MarkdownConverter($environment);
echo $converter->convertToHtml('# Hello World!');
```

Alternatively, if you don't want all of the core Markdown syntax, avoid using `CommonMarkCoreExtension`.  You can always add just the individual parsers, renderers, etc. you actually want with the [`Environment`](/2.0/customization/environment/).  (This is actually how the [Inlines Only Extension](/2.0/extensions/inlines-only/) works - it only includes a subset of things that `CommonMarkCoreExtension` does!)
