---
layout: default
title: Extensions
description: Creating custom extensions to add new syntax and other custom functionality
redirect_from: /0.20/customization/extensions/
---

# Extensions

Extensions provide a way to group related parsers, renderers, etc. together with pre-defined priorities, configuration settings, etc.  They are perfect for distributing your customizations as reusable, open-source packages that others can plug into their own projects!

To create an extension, simply create a new class implementing `ExtensionInterface`.  This has a single method where you're given a `ConfigurableEnvironmentInterface` to register whatever things you need to. For example:

```php
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\ConfigurableEnvironmentInterface;

final class EmojiExtension implements ExtensionInterface
{
    public function register(ConfigurableEnvironmentInterface $environment)
    {
        $environment
            // TODO: Create the EmojiParser, Emoji, and EmojiRenderer classes
            ->addInlineParser(new EmojiParser(), 20)
            ->addInlineRenderer(Emoji::class, new EmojiRenderer(), 0)
        ;
    }
}
```

To hook up your new extension to the `Environment`, simply do this:

```php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;


$environment = Environment::createCommonMarkEnvironment();
$environment->addExtension(new EmojiExtension());

$converter = new CommonMarkConverter([], $environment);
echo $converter->convertToHtml('Hello! :wave:');
```

## Included Extensions

Starting in v1.3, this library includes several extensions to support GitHub-Flavored Markdown.  You can manually add the GFM extension to your environment like this:

```php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;

$environment = Environment::createCommonMarkEnvironment();
$environment->addExtension(new GithubFlavoredMarkdownExtension());

$converter = new CommonMarkConverter([], $environment);
echo $converter->convertToHtml('Hello GFM!');

```

Or, if you only want a subset of GFM extensions, you can add them individually like this instead:

```php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\DisallowedRawHtml\DisallowedRawHtmlExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\TaskList\TaskListExtension;

$environment = Environment::createCommonMarkEnvironment();
// Remove any of the lines below if you don't want a particular feature
$environment->addExtension(new AutolinkExtension());
$environment->addExtension(new DisallowedRawHtmlExtension());
$environment->addExtension(new StrikethroughExtension());
$environment->addExtension(new TableExtension());
$environment->addExtension(new TaskListExtension());

$converter = new CommonMarkConverter([], $environment);
echo $converter->convertToHtml('Hello GFM!');
```

### GFM Extensions

| Extension | Purpose | Documentation |
| --------- | ------- | ------------- |
| **`GithubFlavoredMarkdownExtension`** | Enables full support for GFM.  Includes the following sub-extensions by default: | |
| `AutolinkExtension` | Enables automatic linking of URLs within text without needing to wrap them with Markdown syntax | [Documentation](https://github.com/thephpleague/commonmark/blob/1.3/src/Extension/Autolink/README.md) |
| `DisallowedRawHtmlExtension` | Disables certain kinds of HTML tags that could affect page rendering | |
| `StrikethroughExtension` | Allows using tilde characters (`~~`) for ~strikethrough~ formatting | [Documentation](https://github.com/thephpleague/commonmark/blob/1.3/src/Extension/Strikethrough/README.md) |
| `TableExtension` | Enables you to create HTML tables | [Documentation](https://github.com/thephpleague/commonmark/blob/1.3/src/Extension/Table/README.md) |
| `TaskListExtension` | Allows the creation of task lists | [Documentation](https://github.com/thephpleague/commonmark/blob/1.3/src/Extension/TaskList/README.md) |

### Other Useful Extensions

| Extension | Purpose | Documentation |
| --------- | ------- | ------------- |
| `ExternalLinkExtension` | Tags external links with additional markup | [Documentation](https://github.com/thephpleague/commonmark/blob/1.3/src/Extension/ExternalLink/README.md) |
| `InlinesOnlyExtension` | Only includes standard CommonMark inline elements - perfect for handling comments and other short bits of text where you only want bold, italic, links, etc. | [Documentation](https://github.com/thephpleague/commonmark/blob/1.3/src/Extension/InlinesOnly/README.md) |
| `SmartPunctExtension` | Intelligently converts ASCII quotes, dashes, and ellipses to their fancy Unicode equivalents | [Documentation](https://github.com/thephpleague/commonmark/blob/1.3/src/Extension/SmartPunct/README.md) |
