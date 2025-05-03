---
layout: default
title: Configuration
redirect_from: /configuration/
---

# Configuration

Many aspects of this library's behavior can be tweaked using configuration options.

You can provide an array of configuration options to the `Environment` or converter classes when creating them:

```php
$config = [
    'renderer' => [
        'block_separator' => "\n",
        'inner_separator' => "\n",
        'soft_break'      => "\n",
    ],
    'commonmark' => [
        'enable_em' => true,
        'enable_strong' => true,
        'use_asterisk' => true,
        'use_underscore' => true,
        'unordered_list_markers' => ['-', '*', '+'],
    ],
    'html_input' => 'escape',
    'allow_unsafe_links' => false,
    'max_nesting_level' => PHP_INT_MAX,
    'max_delimiters_per_line' => PHP_INT_MAX,
    'slug_normalizer' => [
        'max_length' => 255,
    ],
];
```

If you're using the basic `CommonMarkConverter` or `GithubFlavoredMarkdown` classes, simply pass the configuration array into the constructor:

```php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\GithubFlavoredMarkdownConverter;

$converter = new CommonMarkConverter($config);
// or
$converter = new GithubFlavoredMarkdownConverter($config);
```

Otherwise, if you're using `MarkdownConverter` to customize the extensions in your parser, pass the configuration into the [Environment](/2.7/customization/environment/)'s constructor instead:

```php
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\InlinesOnly\InlinesOnlyExtension;
use League\CommonMark\MarkdownConverter;

// Here's where we set the configuration array:
$environment = new Environment($config);

// TODO: Add any/all the extensions you wish; for example:
$environment->addExtension(new InlinesOnlyExtension());

// Go forth and convert you some Markdown!
$converter = new MarkdownConverter($environment);
```

Here's a list of the core configuration options available:

- `renderer` - Array of options for rendering HTML
  - `block_separator` - String to use for separating renderer block elements
  - `inner_separator` - String to use for separating inner block contents
  - `soft_break` - String to use for rendering soft breaks
- `html_input` - How to handle HTML input.  Set this option to one of the following strings:
  - `strip` - Strip all HTML (equivalent to `'safe' => true`)
  - `allow` - Allow all HTML input as-is (default value; equivalent to `'safe' => false)
  - `escape` - Escape all HTML
- `allow_unsafe_links` - Remove risky link and image URLs by setting this to `false` (default: `true`)
- `max_nesting_level` - The maximum nesting level for blocks (default: `PHP_INT_MAX`). Setting this to a positive integer can help protect against long parse times and/or segfaults if blocks are too deeply-nested.
- `max_delimiters_per_line` - The maximum number of delimiters (e.g. `*` or `_`) allowed in a single line (default: `PHP_INT_MAX`). Setting this to a positive integer can help protect against long parse times and/or segfaults if lines are too long.
- `slug_normalizer` - Array of options for configuring how URL-safe slugs are created; see [the slug normalizer docs](/2.5/customization/slug-normalizer/#configuration) for more details
  - `instance` - An alternative normalizer to use (defaults to the included `SlugNormalizer`)
  - `max_length` - Limits the size of generated slugs (defaults to 255 characters)
  - `unique` - Controls whether slugs should be unique per `'document'` (default) or per `'environment'`; can be disabled with `false`

Additional configuration options are available for most of the [available extensions](/2.7/customization/extensions/) - refer to their individual documentation for more details.  For example, the CommonMark core extension offers these additional options:

- `commonmark` - Array of options for configuring the CommonMark core extension:
  - `enable_em` - Disable `<em>` parsing by setting to `false`; enable with `true` (default: `true`)
  - `enable_strong` - Disable `<strong>` parsing by setting to `false`; enable with `true` (default: `true`)
  - `use_asterisk` - Disable parsing of `*` for emphasis by setting to `false`; enable with `true` (default: `true`)
  - `use_underscore` - Disable parsing of `_` for emphasis by setting to `false`; enable with `true` (default: `true`)
  - `unordered_list_markers` - Array of characters that can be used to indicate a bulleted list (default: `["-", "*", "+"]`)

## Environment

The configuration is ultimately passed to (and managed via) the `Environment`.  If you're creating your own `Environment`, simply pass your config array into its constructor instead.

[Learn more about customizing the Environment](/2.7/customization/environment/)
