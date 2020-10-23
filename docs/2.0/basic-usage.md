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

Or if you want Github-Flavored Markdown:

```php
use League\CommonMark\GithubFlavoredMarkdownConverter;

$converter = new GithubFlavoredMarkdownConverter();
echo $converter->convertToHtml('# Hello World!');

// <h1>Hello World!</h1>
```

<i class="fa fa-exclamation-triangle"></i>
**Important:** See the [security](/2.0/security/) section for important details on avoiding security misconfigurations.

[Additional customization](/2.0/customization/overview/) is also possible, and we have many handy [extensions](/2.0/extensions/overview/) to enable additional syntax and features.

## Supported Character Encodings

Please note that only UTF-8 and ASCII encodings are supported.  If your Markdown uses a different encoding please convert it to UTF-8 before running it through this library.

## Return Value

The `convertToHtml()` method actually returns an instance of `League\CommonMark\Output\RenderedContentInterface`.  You can cast this (implicitly, as shown above, or explicitly) to a `string` or call `getContent()` to get the final HTML output.
