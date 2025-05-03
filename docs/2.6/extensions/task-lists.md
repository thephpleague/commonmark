---
layout: default
title: Task List Extension
description: The TaskListExtension adds support for GFM-style task lists
---

# Task List Extension

_(Note: this extension is included by default within [the GFM extension](/2.6/extensions/github-flavored-markdown/))_

This extension adds support for [GFM-style task lists](https://github.github.com/gfm/#task-list-items-extension-).

## Installation

This extension is bundled with `league/commonmark`. This library can be installed via Composer:

```bash
composer require league/commonmark
```

See the [installation](/2.6/installation/) section for more details.

## Usage

Configure your `Environment` as usual and simply add the `TaskListExtension` provided by this package:

```php
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\TaskList\TaskListExtension;
use League\CommonMark\MarkdownConverter;

// Define your configuration, if needed
$config = [];

// Configure the Environment with all the CommonMark parsers/renderers
$environment = new Environment($config);
$environment->addExtension(new CommonMarkCoreExtension());

// Add this extension
$environment->addExtension(new TaskListExtension());

// Instantiate the converter engine and start converting some Markdown!
$converter = new MarkdownConverter($environment);

$markdown = <<<EOT
 - [x] Install this extension
 - [ ] ???
 - [ ] Profit!
EOT;

echo $converter->convert($markdown);
```

Please note that this extension doesn't provide any JavaScript functionality to handle people checking and unchecking boxes - you'll need to implement that yourself if needed.
