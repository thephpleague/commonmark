---
layout: default
title: Configuration
---

# Configuration

You can provide an array of configuration options to the `CommonMarkConverter` when creating it:

```php
use League\CommonMark\CommonMarkConverter;

$converter = new CommonMarkConverter([
    'renderer' => [
        'block_separator' => "\n",
        'inner_separator' => "\n",
        'soft_break'      => "\n",
    ],
    'enable_em' => true,
    'enable_strong' => true,
    'use_asterisk' => true,
    'use_underscore' => true,
    'unordered_list_markers' => ['-', '*', '+'],
    'html_input' => 'escape',
    'allow_unsafe_links' => false,
    'max_nesting_level' => INF,
]);
```

Here's a list of currently-supported options:

- `renderer` - Array of options for rendering HTML
  - `block_separator` - String to use for separating renderer block elements
  - `inner_separator` - String to use for separating inner block contents
  - `soft_break` - String to use for rendering soft breaks
- `enable_em` - Disable `<em>` parsing by setting to `false`; enable with `true` (default: `true`)
- `enable_strong` - Disable `<strong>` parsing by setting to `false`; enable with `true` (default: `true`)
- `use_asterisk` - Disable parsing of `*` for emphasis by setting to `false`; enable with `true` (default: `true`)
- `use_underscore` - Disable parsing of `_` for emphasis by setting to `false`; enable with `true` (default: `true`)
- `unordered_list_markers` - Array of characters that can be used to indicated a bulleted list (default: `["-", "*", "+"]`)
- `html_input` - How to handle HTML input.  Set this option to one of the following strings:
  - `strip` - Strip all HTML (equivalent to `'safe' => true`)
  - `allow` - Allow all HTML input as-is (default value; equivalent to `'safe' => false)
  - `escape` - Escape all HTML
- `allow_unsafe_links` - Remove risky link and image URLs by setting this to `false` (default: `true`)
- `max_nesting_level` - The maximum nesting level for blocks (default: infinite). Setting this to a positive integer can help protect against long parse times and/or segfaults if blocks are too deeply-nested. Added in 0.17.

Additional configuration options are available for some of the [available extensions](/1.3/customization/extensions/) - refer to their individual documentation for more details.

## Environment

The configuration is ultimately passed to (and managed via) the `Environment`.  If you're creating your own `Environment`, simply pass your config array into its constructor instead.

The `Environment` also exposes three methods for managing the configuration:

- `setConfig(array $config = [])` - Replace the current configuration with something else
- `mergeConfig(array $config = [])` - Recursively merge the current configuration with the given options
- `getConfig(string $key, $default = null)` - Returns the config value. For nested configs, use a `/`-separate path; for example: `renderer/soft_break`

[Learn more about customizing the Environment](/1.3/customization/environment/)
