---
layout: default
title: Normalize Headings Extension
description: The NormalizeHeadingsExtension constrains heading levels to a configured range
redirect_from:
  - /extensions/normalize-headings/
---

# Normalize Headings Extension

This extension lets you constrain heading levels to a configured range.

For example, if you configure the allowed levels as `2` through `4`:

- `#` headings (`<h1>`) will be converted to `<h2>`
- `#####` and `######` headings (`<h5>` / `<h6>`) will be converted to `<h4>`

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
use League\CommonMark\Extension\NormalizeHeadings\NormalizeHeadingsExtension;
use League\CommonMark\MarkdownConverter;

// Extension defaults are shown below
// If you're happy with the defaults, feel free to remove them from this array
$config = [
    'normalize_headings' => [
        'min_level' => 1,
        'max_level' => 6,
    ],
];

// Configure the Environment with all the CommonMark parsers/renderers
$environment = new Environment($config);
$environment->addExtension(new CommonMarkCoreExtension());

// Add this extension
$environment->addExtension(new NormalizeHeadingsExtension());

// Instantiate the converter engine and start converting some Markdown!
$converter = new MarkdownConverter($environment);
echo $converter->convert("# Heading 1\n\n## Heading 2");
```

## Configuration

This extension can be configured by providing a `normalize_headings` array with two nested options.

### `min_level` and `max_level`

These two settings control the allowed heading range. By default, all six levels (`1` to `6`) are allowed.
The `min_level` value must be less than or equal to `max_level`.

When a parsed heading level falls outside the configured range, it is converted to the nearest boundary:

- below `min_level` -> `min_level`
- above `max_level` -> `max_level`
