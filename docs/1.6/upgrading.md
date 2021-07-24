---
layout: default
title: Upgrading from 1.5 - 1.6
description: Guide to upgrading to newer versions of this library
---

# Upgrading from 1.5 to 1.6

## Configuration changes

The upcoming v2.0 release is going to change the keys/paths for several configuration options. To help prepare for that change, we've added support for the new keys/paths:

| Current Key/Path         | New Key/Path                        | Notes |
| ------------------------ | ----------------------------------- | ----- |
| `enable_em`              | `commonmark/enable_em`              |       |
| `enable_strong`          | `commonmark/enable_strong`          |       |
| `use_asterisk`           | `commonmark/use_asterisk`           |       |
| `use_underscore`         | `commonmark/use_underscore`         |       |
| `unordered_list_markers` | `commonmark/unordered_list_markers` |       |
| `mentions/*/symbol`      | `mentions/*/prefix`                 |       |
| `mentions/*/regex`       | `mentions/*/pattern`                | Should not contain starting/ending `/` delimiters or flags - must be a partial regex |

Additionally, 2.0 will not support using floats for the `max_nesting_level` option.

Version 1.6 will support both the 1.x and 2.0 variations mentioned above but 2.0 won't, so consider changing them now:

```diff
 $config = [
     'html_input' => 'escape',
     'allow_unsafe_links' => false,
-    'max_nesting_level' => INF,
+    'max_nesting_level' => PHP_INT_MAX,
     'renderer' => [
         'block_separator' => "\n",
         'inner_separator' => "\n",
         'soft_break'      => "\n",
     ],
-    'enable_em' => true,
-    'enable_strong' => true,
-    'use_asterisk' => true,
-    'use_underscore' => true,
-    'unordered_list_markers' => ['-', '+', '*'],
+    'commonmark' => [
+        'enable_em' => true,
+        'enable_strong' => true,
+        'use_asterisk' => true,
+        'use_underscore' => true,
+        'unordered_list_markers' => ['-', '+', '*'],
+    ],
     'mentions' => [
         'github_handle' => [
-            'symbol'    => '@',
-            'regex'     => '/[a-z\d](?:[a-z\d]|-(?=[a-z\d])){0,38}(?!\w)/i',
+            'prefix'    => '@',
+            'regex'     => '[a-z\d](?:[a-z\d]|-(?=[a-z\d])){0,38}(?!\w)',
             'generator' => 'https://github.com/%s',
         ],
     ],
 ];
```

## Converters with custom environments

Version 2.0 will no longer allow custom environments to be injected via the constructors of `CommonMarkConverter` or `GithubFlavoredMarkdownConverter`. You should instead use the newly-added `MarkdownConverter` class:

```diff
-use League\CommonMark\CommonMarkConverter;
 use League\CommonMark\Environment;
 use League\CommonMark\Extension\InlinesOnly\InlinesOnlyExtension;
+use League\CommonMark\MarkdownConverter;

 $config = [
     'html_input' => 'escape',
     'allow_unsafe_links' => false,
 ];

 $environment = Environment::createCommonMarkEnvironment();
 $environment->addExtension(new InlinesOnlyExtension());
+$environment->mergeConfig($config);

 // Go forth and convert you some Markdown!
-$converter = new CommonMarkConverter($config, $environment);
+$converter = new MarkdownConverter($environment);
 echo $converter->convertToHtml('# Hello World!');
```

## Environment and Configuration method changes

The environment's `setConfig()` method is now deprecated and will be removed in 2.0 - use `mergeConfig()` instead.

Calling `ConfigurableEnvironmentInterface::mergeConfig()` without the array parameter is deprecated and won't be allowed in 2.0.

Calling `Configuration::getConfig()` without any parameters to retrieve the full configuration is deprecated and won't be allowed in 2.0. Future versions should only fetch the config items they need, not the whole configuration.

Calling `Configuration::set()` without the second `$value` parameter is deprecated and won't be allowed in 2.0.  You should always explicitly define the value you want to be set.

## RegexHelper::matchAll()

The `RegexHelper::matchAll()` method has been deprecated and will be removed in 2.0. Use the new, more-efficient `RegexHelper::matchFirst()` method instead.

## Extending ArrayCollection

The `ArrayCollection` class will be marked `final` in 2.0 so avoid extending it.
