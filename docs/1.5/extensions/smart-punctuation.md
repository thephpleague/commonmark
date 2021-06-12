---
layout: default
title: Smart Punctuation Extension
description: The SmartPunctExtension intelligently converts ASCII quotes, dashes, and ellipses to their Unicode equivalents
---

# Smart Punctuation Extension

The `SmartPunctExtension` Intelligently converts ASCII quotes, dashes, and ellipses to their Unicode equivalents.

For example, this Markdown...

```markdown
"CommonMark is the PHP League's Markdown parser," she said.  "It's super-configurable... you can even use additional extensions to expand its capabilities -- just like this one!"
```

Will result in this HTML:

```html
<p>“CommonMark is the PHP League’s Markdown parser,” she said.  “It’s super-configurable… you can even use additional extensions to expand its capabilities – just like this one!”</p>
```

## Installation

This extension is bundled with `league/commonmark`. This library can be installed via Composer:

```bash
composer require league/commonmark
```

See the [installation](/1.5/installation/) section for more details.

## Usage

Extensions can be added to any new `Environment`:

```php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\SmartPunct\SmartPunctExtension;

// Obtain a pre-configured Environment with all the CommonMark parsers/renderers ready-to-go
$environment = Environment::createCommonMarkEnvironment();

// Add this extension
$environment->addExtension(new SmartPunctExtension());

// Set your configuration
$config = [
    'smartpunct' => [
        'double_quote_opener' => '“',
        'double_quote_closer' => '”',
        'single_quote_opener' => '‘',
        'single_quote_closer' => '’',
    ],
];

// Instantiate the converter engine and start converting some Markdown!
$converter = new CommonMarkConverter($config, $environment);
echo $converter->convertToHtml('# Hello World!');
```
