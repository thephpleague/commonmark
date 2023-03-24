---
layout: default
title: Disallowed Raw HTML Extension
description: The DisallowedRawHtmlExtension automatically escapes certain HTML tags when rendering raw HTML
---

# Disallowed Raw HTML Extension

_(Note: this extension is included by default within [the GFM extension](/2.3/extensions/github-flavored-markdown/))_

The `DisallowedRawHtmlExtension` automatically escapes certain HTML tags when rendering raw HTML, such as:

- `<title>`
- `<textarea>`
- `<style>`
- `<xmp>`
- `<iframe>`
- `<noembed>`
- `<noframes>`
- `<script>`
- `<plaintext>`

Filtering is done by replacing the leading `<` with the entity `&lt;`.

This is required by the [GFM spec](https://github.github.com/gfm/#disallowed-raw-html-extension-) because these particular tags could cause undesirable side-effects if a malicious user tries to introduce them.

All other HTML tags are left untouched by this extension.

## Installation

This extension is bundled with `league/commonmark`. This library can be installed via Composer:

```bash
composer require league/commonmark
```

See the [installation](/2.3/installation/) section for more details.

## Usage

Configure your `Environment` as usual and simply add the `DisallowedRawHtmlExtension` provided by this package:

```php
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\DisallowedRawHtml\DisallowedRawHtmlExtension;
use League\CommonMark\MarkdownConverter;

// Customize the extension's configuration if needed
// Default values are shown below - you can omit this configuration if you're happy with those defaults
// and don't want to customize them
$config = [
    'disallowed_raw_html' => [
        'disallowed_tags' => ['title', 'textarea', 'style', 'xmp', 'iframe', 'noembed', 'noframes', 'script', 'plaintext'],
    ],
];

// Configure the Environment with all the CommonMark parsers/renderers
$environment = new Environment($config);
$environment->addExtension(new CommonMarkCoreExtension());

// Add this extension
$environment->addExtension(new DisallowedRawHtmlExtension());

// Instantiate the converter engine and start converting some Markdown!
$converter = new MarkdownConverter($environment);
echo $converter->convert('I cannot change the page <title>anymore</title>');
```

## Configuration

This extension can be configured by providing a `disallowed_raw_html` array with the following nested configuration options.  The defaults are shown in the code example above.

### `disallowed_tags`

An `array` containing a list of tags that should be escaped.
