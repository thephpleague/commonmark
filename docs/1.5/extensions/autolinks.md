---
layout: default
title: Autolink Extension
description: The Autolink extension automatically converts URLs in plain text to clickable links
---

# Autolink Extension

_(Note: this extension is included by default within [the GFM extension](/1.5/extensions/github-flavored-markdown/))_

The `AutolinkExtension` adds [GFM-style autolinking][link-gfm-spec-autolinking].  It automatically links URLs and email addresses even when the CommonMark `<...>` autolink syntax is not used.

## Installation

This extension is bundled with `league/commonmark`. This library can be installed via Composer:

```bash
composer require league/commonmark
```

See the [installation](/1.5/installation/) section for more details.

## Usage

Configure your `Environment` as usual and simply add the `AutolinkExtension` provided by this package:

```php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;

// Obtain a pre-configured Environment with all the CommonMark parsers/renderers ready-to-go
$environment = Environment::createCommonMarkEnvironment();

// Add this extension
$environment->addExtension(new AutolinkExtension());

// Instantiate the converter engine and start converting some Markdown!
$converter = new CommonMarkConverter([], $environment);
echo $converter->convertToHtml('I successfully installed the https://github.com/thephpleague/commonmark project with the Autolink extension!');
```

## `@mention`-style Autolinking

As of v1.5, [mention autolinking is now handled by a separate Mention extension](/1.5/extensions/mentions/).

[link-gfm-spec-autolinking]: https://github.github.com/gfm/#autolinks-extension-
