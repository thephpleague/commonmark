---
layout: default
title: Normalize Headings Extension
description: The NormalizeHeadingsExtension rewrites heading levels to produce a valid outline within a configured range
redirect_from:
  - /extensions/normalize-headings/
---

# Normalize Headings Extension

This extension rewrites the heading levels in your document so that the resulting HTML is valid and
sits wherever you need it to.

It always repairs headings which skip a level - an `<h1>` followed by an `<h3>` is invalid HTML, and
becomes an `<h1>` followed by an `<h2>`. Documents whose headings are already valid are left alone.

It can also constrain headings to a configured range of levels. For example, if you configure the
allowed levels as `2` through `4`:

- `#` headings (`<h1>`) will be converted to `<h2>`
- `#####` and `######` headings (`<h5>` / `<h6>`) will be converted to `<h4>`

And it can optionally rebase each document so that its headings always begin at that lowest level,
regardless of which level the author started at.

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
        'rebase_to_min_level' => false,
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

## Repairing Skipped Levels

The [W3C validator](https://validator.w3.org/) reports an error when a heading descends by more than one
level at a time - an `<h1>` may be followed by an `<h2>`, but not by an `<h3>`. (Ascending by any amount
is fine.)

This extension always rewrites headings so that each one descends at most one level below the heading
it's nested under. A heading's new level is based on how deeply it's nested, so headings which were
siblings in the original document remain siblings:

```markdown
# Header 1

#### Header 4a

#### Header 4b

# Header 1a

### Header 3
```

produces `<h1>`, `<h2>`, `<h2>`, `<h1>`, `<h2>`.

Documents whose headings are already valid come out unchanged.

## Configuration

This extension can be configured by providing a `normalize_headings` array with three nested options.

### `min_level` and `max_level`

These two settings control the allowed heading range. By default, all six levels (`1` to `6`) are allowed.
The `min_level` value must be less than or equal to `max_level`.

When a heading falls outside the configured range, it is converted to the nearest boundary:

- below `min_level` -> `min_level`
- above `max_level` -> `max_level`

Setting `min_level` and `max_level` to the same value will therefore flatten every heading to that level.

### `rebase_to_min_level`

By default, top-level headings keep the level they were written at, so a document which deliberately
starts at `<h3>` stays there and only its skipped levels are repaired. This respects an author who starts
at `<h2>` because the surrounding page template already provides the `<h1>`.

When `rebase_to_min_level` is enabled, top-level headings are moved to `min_level` instead, so that every
document begins at a known level regardless of how it was written. Consider a document which starts at
`<h3>`:

```markdown
### Introduction

##### A Nested Heading

### Conclusion
```

With the default `min_level` of `1`:

| `rebase_to_min_level` | Output |
| --------------------- | ------ |
| `false` (default) | `<h3>`, `<h4>`, `<h3>` |
| `true` | `<h1>`, `<h2>`, `<h1>` |

This works in both directions: with a `min_level` of `2`, a document starting at `<h1>` would be moved
*down* so that its top-level headings become `<h2>`.
