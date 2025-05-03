---
layout: default
title: Autolink Extension
description: The Autolink extension automatically converts URLs in plain text to clickable links
---

# Autolink Extension

_(Note: this extension is included by default within [the GFM extension](/2.6/extensions/github-flavored-markdown/))_

The `AutolinkExtension` adds [GFM-style autolinking][link-gfm-spec-autolinking].  It automatically links URLs and email addresses even when the CommonMark `<...>` autolink syntax is not used.

## Installation

This extension is bundled with `league/commonmark`. This library can be installed via Composer:

```bash
composer require league/commonmark
```

See the [installation](/2.6/installation/) section for more details.

## Usage

Configure your `Environment` as usual and simply add the `AutolinkExtension` provided by this package:

```php
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

// Define your configuration, if needed
$config = [
    'autolink' => [
        'allowed_protocols' => ['https'], // defaults to ['https', 'http', 'ftp']
        'default_protocol' => 'https', // defaults to 'http'
    ],
];

// Configure the Environment with all the CommonMark parsers/renderers
$environment = new Environment($config);
$environment->addExtension(new CommonMarkCoreExtension());

// Add this extension
$environment->addExtension(new AutolinkExtension());

// Instantiate the converter engine and start converting some Markdown!
$converter = new MarkdownConverter($environment);
echo $converter->convert('I successfully installed the https://github.com/thephpleague/commonmark project with the Autolink extension!');
```

## Configuration

As of version 2.6.0, this extension supports the following configuration options under the `autolink` configuration:

### `allowed_protocols` option

This option defines which types of URLs will be autolinked. The default value of `['https', 'http', 'ftp']` means that only URLs using those protocols will be autolinked. Setting this to just `['https']` means that only HTTPS URLs will be autolinked.

### `default_protocol` option

This option defines the default protocol for URLs that start with `www.` and don't have an explicit protocol set. For example, setting this to `https` would convert `www.example.com` to `https://www.example.com`.

## `@mention`-style Autolinking

As of v1.5, [mention autolinking is now handled by a Mention Parser outside of this extension](/2.6/extensions/mentions/).

[link-gfm-spec-autolinking]: https://github.github.com/gfm/#autolinks-extension-
