---
layout: default
title: Disallowed Raw HTML Extension
description: The DisallowedRawHtmlExtension automatically escapes certain HTML tags when rendering raw HTML
---

# Disallowed Raw HTML Extension

_(Note: this extension is included by default within [the GFM extension](/1.4/extensions/github-flavored-markdown/))_

The `DisallowedRawHtmlExtension` automatically filters certain HTML tags when rendering output, such as:

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

See the [installation](/1.4/installation/) section for more details.

## Usage

Configure your `Environment` as usual and simply add the `DisallowedRawHtmlExtension` provided by this package:

```php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\DisallowedRawHtml\DisallowedRawHtmlExtension;

// Obtain a pre-configured Environment with all the CommonMark parsers/renderers ready-to-go
$environment = Environment::createCommonMarkEnvironment();

// Add this extension
$environment->addExtension(new DisallowedRawHtmlExtension());

// Instantiate the converter engine and start converting some Markdown!
$converter = new CommonMarkConverter([], $environment);
echo $converter->convertToHtml('I cannot change the page <title>anymore</title>');
```
