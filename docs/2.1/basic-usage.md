---
layout: default
title: Basic Usage
description: Basic usage of the CommonMark parser
---

# Basic Usage

<i class="fa fa-exclamation-triangle"></i>
**Important:** See the [security](/2.1/security/) section for important details on avoiding security misconfigurations.

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

## Using Extensions

The `CommonMarkConverter` and `GithubFlavoredMarkdownConverter` shown above automatically configure [the environment](/2.1/customization/environment/) for you, but if you want to use [additional extensions](/2.1/customization/extensions/) you'll need to avoid those classes and use the generic `MarkdownConverter` class instead to customize [the environment](/2.1/customization/environment/) with whatever extensions you wish to use:

```php
require __DIR__ . '/vendor/autoload.php';

use League\CommonMark\Environment\Environment;
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

## Configuration

If you're using the `CommonMarkConverter` or `GithubFlavoredMarkdownConverter` class you can pass configuration options directly into their constructor:

```php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\GithubFlavoredMarkdownConverter;

$converter = new CommonMarkConverter($config);
// or
$converter = new GithubFlavoredMarkdownConverter($config);
```

Otherwise, if you’re using `MarkdownConverter` to customize the extensions in your parser, pass the configuration into the `Environment`'s constructor instead:

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

See the [configuration section](/2.1/configuration/) for more information on the available configuration options.

## Supported Character Encodings

Please note that only UTF-8 and ASCII encodings are supported.  If your Markdown uses a different encoding please convert it to UTF-8 before running it through this library.

## Return Value

The `convertToHtml()` method actually returns an instance of `League\CommonMark\Output\RenderedContentInterface`.  You can cast this (implicitly, as shown above, or explicitly) to a `string` or call `getContent()` to get the final HTML output.
