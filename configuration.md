---
layout: default
title: Configuration
permalink: /configuration/
---

Configuration
=============

You can provide an array of configuration options to the `CommonMarkConverter` when creating it::

~~~
use League\CommonMark\CommonMarkConverter;

$converter = new CommonMarkConverter([
    'renderer' => [
        'block_separator' => "\n",
        'inner_separator' => "\n",
        'soft_break'      => "\n",
    ],
]);
~~~

Here's a list of currently-supported options:

* `renderer` - Array of options for rendering HTML
  * `block_separator` - String to use for separating renderer block elements
  * `inner_separator` - String to use for separating inner block contents 
  * `soft_break` - String to use for rendering soft breaks

## Environment

The configuration is ultimately passed to (and managed via) the `Environment`.  If you're creating your own `Environment`, simply pass your config array into its constructor instead.

The `Environment` also exposes three methods for managing the configuration:

* `setConfig(array $config = [])` - Replace the current configuration with something else
* `mergeConfig(array $config = [])` - Recursively merge the current configuration with the given options
* `getConfig(string $key, $default = null)` - Returns the config value. For nested configs, use a `/`-separate path; for example: `renderer/soft_break`
