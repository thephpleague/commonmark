---
layout: default
title: Extensions Overview
description: An overview of the extensions included with this library
redirect_from:
- /extensions/
- /extensions/overview/
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
| [Attributes] | Add HTML attributes (like `id` and `class`) from within the Markdown content | `1.5.0` | |
| [Autolinks] | Enables automatic linking of URLs within text without needing to wrap them with Markdown syntax | `1.3.0`  | <i class="fab fa-github"></i> |
| [Default Attributes] | Easily apply default HTML classes using configuration options to match your site's styles  | `2.0.0` | |
| [Description Lists] | Create `<dl>` description lists using Markdown Extra's syntax | `2.0.0` | |
| [Disallowed Raw HTML] | Disables certain kinds of HTML tags that could affect page rendering | `1.3.0`  | <i class="fab fa-github"></i> |
| [Embed] | Embed rich content (like videos, tweets, and more) from other websites | `2.3.0` | |
| [External Links] | Tags external links with additional markup | `1.3.0` | |
| [Footnotes] | Add footnote references throughout the document and show a listing of them at the bottom | `1.5.0` | |
| [Front Matter] | Parses YAML front matter from your Markdown input |  `2.0.0` | |
| **[GitHub Flavored Markdown]** | Enables full support for GFM. Automatically includes the extensions noted in the `GFM` column (though you can certainly add them individually if you wish): | `1.3.0` | |
| [Heading Permalinks] | Makes heading elements linkable | `1.4.0` | |
| [Inlines Only] | Only includes standard CommonMark inline elements - perfect for handling comments and other short bits of text where you only want bold, italic, links, etc. | `1.3.0` | |
| [Mentions] | Easy parsing of `@mention` and `#123`-style references | `1.5.0` | |
| [Strikethrough] | Allows using tilde characters (`~~`) for ~strikethrough~ formatting | `1.3.0`  | <i class="fab fa-github"></i> |
| [Tables] | Enables you to create HTML tables | `1.3.0`  | <i class="fab fa-github"></i> |
| [Table of Contents] | Automatically inserts links to the headings at the top of your document | `1.4.0` | |
| [Task Lists] | Allows the creation of task lists | `1.3.0`  | <i class="fab fa-github"></i> |
| [Smart Punctuation] | Intelligently converts ASCII quotes, dashes, and ellipses to their fancy Unicode equivalents | `1.3.0` | |

## Usage

You can enable extensions by simply calling `->addExtension()` on the [Environment](/2.6/customization/environment/).

In an effort to streamline the extensions used in GitHub Flavored Markdown (GFM), a special extension named
`GithubFlavoredMarkdownExtension` can be used that will automatically add all the extensions checked in the `GFM`
column above for you:

```php
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;

// Define your configuration, if needed
$config = [];

// Configure the Environment with all the extensions you need
$environment = new Environment($config);
$environment->addExtension(new CommonMarkCoreExtension());
$environment->addExtension(new GithubFlavoredMarkdownExtension());

$converter = new MarkdownConverter($environment);
echo $converter->convert('Hello World!');
```

Or maybe you only want a subset of GFM extensions, plus the [Smart Punctuation extension](/2.6/extensions/smart-punctuation/):

```php
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\DisallowedRawHtml\DisallowedRawHtmlExtension;
use League\CommonMark\Extension\SmartPunct\SmartPunctExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter;

// Define your configuration, if needed
$config = [];

// Configure the Environment with all the CommonMark parsers/renderers
$environment = new Environment($config);
$environment->addExtension(new CommonMarkCoreExtension());

// Add the other extensions you need
$environment->addExtension(new AutolinkExtension());
$environment->addExtension(new DisallowedRawHtmlExtension());
$environment->addExtension(new SmartPunctExtension());
$environment->addExtension(new StrikethroughExtension());
$environment->addExtension(new TableExtension());

$converter = new MarkdownConverter($environment);
echo $converter->convert('Hello World!');
```

The extension system makes it easy to mix-and-match extensions to fit your needs.

## Writing Custom Extensions

See the [Custom Extensions](/2.6/customization/extensions/) page for details on how you can create your own custom extensions.

[Attributes]: /2.6/extensions/attributes/
[Autolinks]: /2.6/extensions/autolinks/
[Default Attributes]: /2.6/extensions/default-attributes/
[Description Lists]: /2.6/extensions/description-lists/
[Disallowed Raw HTML]: /2.6/extensions/disallowed-raw-html/
[Embed]: /2.6/extensions/embed/
[External Links]: /2.6/extensions/external-links/
[Footnotes]: /2.6/extensions/footnotes/
[Front Matter]: /2.6/extensions/front-matter/
[GitHub Flavored Markdown]: /2.6/extensions/github-flavored-markdown/
[Heading Permalinks]: /2.6/extensions/heading-permalinks/
[Inlines Only]: /2.6/extensions/inlines-only/
[Mentions]: /2.6/extensions/mentions/
[Strikethrough]: /2.6/extensions/strikethrough/
[Tables]: /2.6/extensions/tables/
[Table of Contents]: /2.6/extensions/table-of-contents/
[Task Lists]: /2.6/extensions/task-lists/
[Smart Punctuation]: /2.6/extensions/smart-punctuation/
