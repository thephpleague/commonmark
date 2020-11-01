---
layout: default
title: Strikethrough Extension
description: The StrikethroughExtension intelligently converts ASCII quotes, dashes, and ellipses to their Unicode equivalents
---

# Strikethrough Extension

_(Note: this extension is included by default within [the GFM extension](/1.3/extensions/github-flavored-markdown/))_

This extension adds support for GFM-style strikethrough syntax.  It allows users to use `~~` in order to indicate text that should be rendered within `<del>` tags.

## Installation

This extension is bundled with `league/commonmark`. This library can be installed via Composer:

```bash
composer require league/commonmark
```

See the [installation](/1.3/installation/) section for more details.

## Usage

This extension can be added to any new `Environment`:

```php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;

// Obtain a pre-configured Environment with all the CommonMark parsers/renderers ready-to-go
$environment = Environment::createCommonMarkEnvironment();

// Add this extension
$environment->addExtension(new StrikethroughExtension());

// Instantiate the converter engine and start converting some Markdown!
$converter = new CommonMarkConverter($config, $environment);
echo $converter->convertToHtml('This extension is ~~really good~~ great!');
```
