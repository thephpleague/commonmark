---
layout: default
title: Extensions Overview
description: An overview of the extensions included with this library
---

Extensions Overview
===================

Extensions provide a simple way to add new syntax and features to the CommonMark parser.  Enabling them is as simple as calling `->addExtension()` on the [Environment](/2.0/customization/environment/).

For example, if you wanted to enable Github-Flavored Markdown, simply do this:

```php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;

$environment = Environment::createCommonMarkEnvironment();
$environment->addExtension(new GithubFlavoredMarkdownExtension());

$converter = new CommonMarkConverter([], $environment);
echo $converter->convertToHtml('Hello World!');
```

Or maybe you only want a subset of GFM extensions, plus the [Smart Punctuation extension](/2.0/extensions/smart-punctuation/):

```php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
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

## Included Extensions

Starting in v1.3, this library includes several extensions to support Github-Flavored Markdown and other common use-cases.

### GFM Extensions

Full GFM syntax is supported with these extensions:

| Extension | Purpose | Documentation |
| --------- | ------- | ------------- |
| **`GithubFlavoredMarkdownExtension`** | Enables full support for GFM.  Includes the following sub-extensions by default (though you can certainly use them separately if you wish): | [Documentation](/2.0/extensions/github-flavored-markdown/) |
| `AutolinkExtension` | Enables automatic linking of URLs within text without needing to wrap them with Markdown syntax | [Documentation](/2.0/extensions/autolinks/) |
| `DisallowedRawHtmlExtension` | Disables certain kinds of HTML tags that could affect page rendering | [Documentation](/2.0/extensions/disallowed-raw-html/) |
| `StrikethroughExtension` | Allows using tilde characters (`~~`) for ~strikethrough~ formatting | [Documentation](/2.0/extensions/strikethrough/) |
| `TableExtension` | Enables you to create HTML tables | [Documentation](/2.0/extensions/tables/) |
| `TaskListExtension` | Allows the creation of task lists | [Documentation](/2.0/extensions/task-lists/) |

### Other Useful Extensions

These extensions are not part of GFM, but can be useful in many cases:

| Extension | Purpose | Documentation |
| --------- | ------- | ------------- |
| `AttributesExtension` | Add HTML attributes (like `id` and `class`) from within the Markdown content | [Documentation](/2.0/extensions/attributes/) |
| `ExternalLinkExtension` | Tags external links with additional markup | [Documentation](/2.0/extensions/external-links/) |
| `FootnoteExtension` | Add footnote references throughout the document and show a listing of them at the bottom | [Documentation](/2.0/extensions/footnotes/) |
| `HeadingPermalinkExtension` | Makes heading elements linkable | [Documentation](/2.0/extensions/heading-permalinks/) |
| `InlinesOnlyExtension` | Only includes standard CommonMark inline elements - perfect for handling comments and other short bits of text where you only want bold, italic, links, etc. | [Documentation](/2.0/extensions/inlines-only/) |
| `MentionParser` | Easy parsing of `@mention` and `#123`-style references | [Documentation](/2.0/extensions/mention/) |
| `TableOfContentsExtension` | Automatically inserts links to the headings at the top of your document | [Documentation](/2.0/extensions/table-of-contents/) |
| `SmartPunctExtension` | Intelligently converts ASCII quotes, dashes, and ellipses to their fancy Unicode equivalents | [Documentation](/2.0/extensions/smart-punctuation/) |


## Writing Custom Extensions

See the [Custom Extensions](/2.0/customization/extensions/) page for details on how you can create your own custom extensions.
