---
layout: default
title: Inlines Only Extension
description: The InlinesOnlyExtension only enables parsing of inline elements
---

# Inlines Only Extension

This extension configures the parser to only render inline elements - no paragraph tags, headers, code blocks, etc.  This makes it perfect for commenting systems where you only want users having bold, italics, links, etc.

## Installation

This extension is bundled with `league/commonmark`. This library can be installed via Composer:

```bash
composer require league/commonmark
```

See the [installation](/1.5/installation/) section for more details.

## Usage

Although you normally add extra extensions along with [the default CommonMark Core extension](/1.5/extensions/commonmark/), we're not going to do that here, because this is essentially a slimmed-down version of the core extension:

```php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\InlinesOnly\InlinesOnlyExtension;

// Create a new, empty environment
$environment = new Environment();

// Add this extension
$environment->addExtension(new InlinesOnlyExtension());

// Instantiate the converter engine and start converting some Markdown!
$converter = new CommonMarkConverter($config, $environment);
echo $converter->convertToHtml('**Hello World!**');
```
