---
layout: default
title: Description List Extension
description: The Description List extension adds support for Markdown Extra-style <dl> lists
---

# Description List Extension

The `DescriptionListExtension` adds [Markdown Extra-style description lists][link-markdown-extra-dl] to facilitate the creation of `<dl>`, `<dt>`, and `<dd>` HTML using Markdown.

## Installation

This extension is bundled with `league/commonmark`. This library can be installed via Composer:

```bash
composer require league/commonmark
```

See the [installation](/2.6/installation/) section for more details.

## Usage

Configure your `Environment` as usual and simply add the `DescriptionListExtension` provided by this package:

```php
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\DescriptionList\DescriptionListExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

// Define your configuration, if needed
$config = [];

// Configure the Environment with all the CommonMark parsers/renderers
$environment = new Environment($config);
$environment->addExtension(new CommonMarkCoreExtension());

// Add this extension
$environment->addExtension(new DescriptionListExtension());

// Instantiate the converter engine and start converting some Markdown!
$converter = new MarkdownConverter($environment);
echo $converter->convert('Some markdown goes here');
```

## Syntax

The syntax is based directly on the rules and logic implemented by the [Markdown Extra library][link-markdown-extra-dl].  Here are some examples of sample Markdown input and HTML output demonstrating the syntax:

```md
Apple
:   Pomaceous fruit of plants of the genus Malus in
    the family Rosaceae.
:   An American computer company.

Orange
:   The fruit of an evergreen tree of the genus Citrus.
```

```html
<dl>
    <dt>Apple</dt>
    <dd>Pomaceous fruit of plants of the genus Malus in
    the family Rosaceae.</dd>
    <dd>An American computer company.</dd>

    <dt>Orange</dt>
    <dd>The fruit of an evergreen tree of the genus Citrus.</dd>
</dl>
```

See the [Markdown Extra documentation][link-markdown-extra-dl] or [our own spec][link-commonmark-description-list-spec] for additional examples.

[link-markdown-extra-dl]: https://michelf.ca/projects/php-markdown/extra/#def-list
[link-commonmark-description-list-spec]: https://github.com/thephpleague/commonmark/blob/2.0/tests/functional/Extension/DescriptionList/spec.txt
