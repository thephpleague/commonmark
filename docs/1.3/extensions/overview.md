---
layout: default
title: Extensions Overview
description: An overview of the extensions included with this library
---

# Extensions Overview

Extensions provide a simple way to add new syntax and features to the CommonMark parser.

## Included Extensions

Starting with version `1.3.0`, this library includes several extensions to support GitHub Flavored Markdown (GFM) and
many other common use-cases. Most of these extensions started out as 3rd-party community based extensions that have
since been officially adopted by this library in an effort to ensure future compatibility and to provide an easy way
to enhance your experience out-of-the-box depending on your specific use-cases.

| Extension | Purpose | Version Introduced | GFM |
| --------- | ------- | ------------------ | --- |
| [Autolinks] | Enables automatic linking of URLs within text without needing to wrap them with Markdown syntax | `1.3.0`  | <i class="fab fa-github"></i> |
| [Disallowed Raw HTML] | Disables certain kinds of HTML tags that could affect page rendering | `1.3.0`  | <i class="fab fa-github"></i> |
| [External Links] | Tags external links with additional markup | `1.3.0` | |
| **[GitHub Flavored Markdown]** | Enables full support for GFM. Automatically includes the extensions noted in the `GFM` column (though you can certainly add them individually if you wish): | `1.3.0` | |
| [Inlines Only] | Only includes standard CommonMark inline elements - perfect for handling comments and other short bits of text where you only want bold, italic, links, etc. | `1.3.0` | |
| [Strikethrough] | Allows using tilde characters (`~~`) for ~strikethrough~ formatting | `1.3.0`  | <i class="fab fa-github"></i> |
| [Tables] | Enables you to create HTML tables | `1.3.0`  | <i class="fab fa-github"></i> |
| [Task Lists] | Allows the creation of task lists | `1.3.0`  | <i class="fab fa-github"></i> |
| [Smart Punctuation] | Intelligently converts ASCII quotes, dashes, and ellipses to their fancy Unicode equivalents | `1.3.0` | |

## Usage

You can enable extensions by simply calling `->addExtension()` on the [Environment](/1.3/customization/environment/).

In an effort to streamline the extensions used in GitHub Flavored Markdown (GFM), a special extension named
`GithubFlavoredMarkdownExtension` can be used that will automatically add all the extensions checked in the `GFM`
column above for you:

```php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;

$environment = Environment::createCommonMarkEnvironment();
$environment->addExtension(new GithubFlavoredMarkdownExtension());

$converter = new CommonMarkConverter([], $environment);
echo $converter->convertToHtml('Hello World!');
```

Or maybe you only want a subset of GFM extensions, plus the [Smart Punctuation extension](/1.3/extensions/smart-punctuation/):

```php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\DisallowedRawHtml\DisallowedRawHtmlExtension;
use League\CommonMark\Extension\SmartPunct\SmartPunctExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\Extension\Table\TableExtension;

$environment = Environment::createCommonMarkEnvironment();
$environment->addExtension(new AutolinkExtension());
$environment->addExtension(new DisallowedRawHtmlExtension());
$environment->addExtension(new SmartPunctExtension());
$environment->addExtension(new StrikethroughExtension());
$environment->addExtension(new TableExtension());

$converter = new CommonMarkConverter([], $environment);
echo $converter->convertToHtml('Hello World!');
```

The extension system makes it easy to mix-and-match extensions to fit your needs.

## Writing Custom Extensions

See the [Custom Extensions](/1.3/customization/extensions/) page for details on how you can create your own custom extensions.

[Autolinks]: /1.3/extensions/autolinks/
[Disallowed Raw HTML]: /1.3/extensions/disallowed-raw-html/
[External Links]: /1.3/extensions/external-links/
[GitHub Flavored Markdown]: /1.3/extensions/github-flavored-markdown/
[Inlines Only]: /1.3/extensions/inlines-only/
[Strikethrough]: /1.3/extensions/strikethrough/
[Tables]: /1.3/extensions/tables/
[Task Lists]: /1.3/extensions/task-lists/
[Smart Punctuation]: /1.3/extensions/smart-punctuation/
