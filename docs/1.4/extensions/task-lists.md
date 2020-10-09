---
layout: default
title: Task List Extension
description: The TaskListExtension adds support for GFM-style task lists
---

# Task List Extension

_(Note: this extension is included by default within [the GFM extension](/1.4/extensions/github-flavored-markdown/))_

This extension adds support for [GFM-style task lists](https://github.github.com/gfm/#task-list-items-extension-).

## Installation

This extension is bundled with `league/commonmark`. This library can be installed via Composer:

```bash
composer require league/commonmark
```

See the [installation](/1.4/installation/) section for more details.

## Usage

Configure your `Environment` as usual and simply add the `TaskListExtension` provided by this package:

```php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\TaskList\TaskListExtension;

// Obtain a pre-configured Environment with all the CommonMark parsers/renderers ready-to-go
$environment = Environment::createCommonMarkEnvironment();

// Add this extension
$environment->addExtension(new TaskListExtension());

// Instantiate the converter engine and start converting some Markdown!
$converter = new CommonMarkConverter([], $environment);

$markdown = <<<EOT
 - [x] Install this extension
 - [ ] ???
 - [ ] Profit!
EOT;

echo $converter->convertToHtml($markdown);
```

Please note that this extension doesn't provide any JavaScript functionality to handle people checking and unchecking boxes - you'll need to implement that yourself if needed.
