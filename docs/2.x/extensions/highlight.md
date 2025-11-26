---
layout: default
title: Highlight Extension
description: The HighlightExtension allows marking important text.
redirect_from:
  - /extensions/highlight/
---

# Highlight Extension

This extension adds support for highlighting important text using the `==` syntax. For example, the Markdown:

```markdown
I need to highlight these ==very important words==.
```

Would be rendered to HTML as:

```html
<p>I need to highlight these <mark>very important words</mark>.</p>
```

Which could then be styled using CSS to produce a highlighter effect.

## Installation

This extension is bundled with `league/commonmark`. This library can be installed via Composer:

```bash
composer require league/commonmark
```

See the [installation](/2.x/installation/) section for more details.

## Usage

This extension can be added to any new `Environment`:

```php
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Highlight\HighlightExtension;
use League\CommonMark\MarkdownConverter;

// Define your configuration, if needed
$config = [];

// Configure the Environment with all the CommonMark parsers/renderers
$environment = new Environment($config);
$environment->addExtension(new CommonMarkCoreExtension());

// Add this extension
$environment->addExtension(new HighlightExtension());

// Instantiate the converter engine and start converting some Markdown!
$converter = new MarkdownConverter($environment);
echo $converter->convert('I need to highlight these ==very important words==.');
```
