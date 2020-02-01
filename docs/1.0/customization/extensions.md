---
layout: default
title: Extensions
description: Creating custom extensions to add new syntax and other custom functionality
redirect_from: /0.20/customization/extensions/
---

Extensions
==========

Extensions provide a way to group related parsers, renderers, etc. together with pre-defined priorities, configuration settings, etc.  They are perfect for distributing your customizations as reusable, open-source packages that others can plug into their own projects!

To create an extension, simply create a new class implementing `ExtensionInterface`.  This has a single method where you're given a `ConfigurableEnvironmentInterface` to register whatever things you need to. For example:

```php
final class EmojiExtension implements ExtensionInterface
{
    public function register(ConfigurableEnvironmentInterface $environment)
    {
        $environment
            ->addInlineParser(new EmojiParser(), 20)
            ->addInlineRenderer(Emoji::class, new EmojiRenderer(), 0)
        ;
    }
}
```

To hook up your new extension to the `Environment`, simply do this:

```php
$environment = Environment::createCommonMarkExtension();
$environment->addExtension(new EmojiExtension();

$converter = new CommonMarkConverter([], $environment);
echo $converter->convertToHtml('Hello! :wave:');
```

## Included Extensions

Starting in v1.3, this library includes several extensions to support Github-Flavored Markdown.  You can manually add the GFM extension to your environment like this:

```php
$environment = Environment::createCommonMarkExtension();
$environment->addExtension(new GithubFlavoredMarkdownExtension();

$converter = new CommonMarkConverter([], $environment);
echo $converter->convertToHtml('Hello GFM!');

```

Or, if you only want a subset of GFM extensions, you can add them individually like this instead:

```php
$environment = Environment::createCommonMarkExtension();
// Remove any of the lines below if you don't want those features
$environment->addExtension(new AutolinkExtension());
$environment->addExtension(new DisallowedRawHtmlExtension());
$environment->addExtension(new StrikethroughExtension());
$environment->addExtension(new TableExtension());
$environment->addExtension(new TaskListExtension());

$converter = new CommonMarkConverter([], $environment);
echo $converter->convertToHtml('Hello GFM!');
```
