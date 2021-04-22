---
layout: default
title: Basic Usage
description: Basic usage of the CommonMark parser
---

# Basic Usage

The `CommonMarkConverter` class provides a simple wrapper for converting Markdown to HTML:

```php
require __DIR__ . '/vendor/autoload.php';

use League\CommonMark\CommonMarkConverter;

$converter = new CommonMarkConverter();
echo $converter->convertToHtml('# Hello World!');

// <h1>Hello World!</h1>
```

Or if you want GitHub-Flavored Markdown:

```php
require __DIR__ . '/vendor/autoload.php';

use League\CommonMark\GithubFlavoredMarkdownConverter;

$converter = new GithubFlavoredMarkdownConverter();
echo $converter->convertToHtml('# Hello World!');

// <h1>Hello World!</h1>
```

Or you can use the generic `MarkdownConverter` class to customize [the environment](/1.6/customization/environment/) with whatever extensions you wish to use:

```php
require __DIR__ . '/vendor/autoload.php';

use League\CommonMark\Environment;
use League\CommonMark\Extension\InlinesOnly\InlinesOnlyExtension;
use League\CommonMark\Extension\SmartPunct\SmartPunctExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\MarkdownConverter;

$environment = new Environment();

$environment->addExtension(new InlinesOnlyExtension());
$environment->addExtension(new SmartPunctExtension());
$environment->addExtension(new StrikethroughExtension());

$converter = new MarkdownConverter($environment);
echo $converter->convertToHtml('**Hello World!**');

// <p><strong>Hello World!</strong></p>
```

<i class="fa fa-exclamation-triangle"></i>
**Important:** See the [security](/1.6/security/) section for important details on avoiding security misconfigurations.

[Additional customization](/1.6/customization/overview/) is also possible, and we have many handy [extensions](/1.6/extensions/overview/) to enable additional syntax and features.

## Supported Character Encodings

Please note that only UTF-8 and ASCII encodings are supported.  If your Markdown uses a different encoding please convert it to UTF-8 before running it through this library.
