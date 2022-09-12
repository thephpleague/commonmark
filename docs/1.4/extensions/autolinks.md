---
layout: default
title: Autolink Extension
description: The Autolink extension automatically converts URLs in plain text to clickable links
---

# Autolink Extension

_(Note: this extension is included by default within [the GFM extension](/1.4/extensions/github-flavored-markdown/))_

The `AutolinkExtension` adds [GFM-style autolinking][link-gfm-spec-autolinking].  It automatically links URLs and email addresses even when the CommonMark `<...>` autolink syntax is not used.

It also provides a parser to autolink `@mentions` to Twitter, GitHub, or any custom service you wish, though this is disabled by default.

## Installation

This extension is bundled with `league/commonmark`. This library can be installed via Composer:

```bash
composer require league/commonmark
```

See the [installation](/1.4/installation/) section for more details.

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

This extension also provides functionality to automatically link "mentions" like `@colinodell` to Twitter, GitHub, or any other site of your choice!

For Twitter:

```php
use League\CommonMark\Environment;
use League\CommonMark\Extension\Autolink\InlineMentionParser;

$environment = Environment::createCommonMarkEnvironment();
$environment->addInlineParser(InlineMentionParser::createTwitterHandleParser());

// TODO: Instantiate your converter and convert some Markdown
```

For GitHub:

```php
use League\CommonMark\Environment;
use League\CommonMark\Extension\Autolink\InlineMentionParser;

$environment = Environment::createCommonMarkEnvironment();
$environment->addInlineParser(InlineMentionParser::createGithubHandleParser());

// TODO: Instantiate your converter and convert some Markdown
```

Or configure your own custom one:

```php
use League\CommonMark\Environment;
use League\CommonMark\Extension\Autolink\InlineMentionParser;

$environment = Environment::createCommonMarkEnvironment();
$environment->addInlineParser(new InlineMentionParser('https://www.example.com/users/%s/profile'));

// TODO: Instantiate your converter and convert some Markdown
```

When creating your own, you can provide two parameters to the constructor:

- A URL template where `%s` is replaced with the username (required)
- A regular expression to parse and validate the username (optional - defaults to `'/^[A-Za-z0-9_]+(?!\w)/'`)

Note that `@mention`-style linking doesn't actually require you to add the extension - just the `InlineParser` of your choice.

[link-gfm-spec-autolinking]: https://github.github.com/gfm/#autolinks-extension-
