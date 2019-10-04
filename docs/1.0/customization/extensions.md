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
