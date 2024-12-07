---
layout: default
title: GitHub-Flavored Markdown
description: The GithubFlavoredMarkdownExtension class includes all the GFM addons
---

# GitHub-Flavored Markdown

You can manually add the GFM extension to your environment like this:

```php
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;

// Define your configuration, if needed
$config = [];

// Configure the Environment with all the CommonMark and GFM parsers/renderers
$environment = new Environment($config);
$environment->addExtension(new CommonMarkCoreExtension());
$environment->addExtension(new GithubFlavoredMarkdownExtension());

$converter = new MarkdownConverter($environment);
echo $converter->convert('Hello GFM!');
```

This will automatically include all of these sub-extensions/features for you:

- [Autolinks](/2.5/extensions/autolinks/)
- [Disallowed Raw HTML](/2.5/extensions/disallowed-raw-html/)
- [Strikethrough](/2.5/extensions/strikethrough/)
- [Tables](/2.5/extensions/tables/)
- [Task Lists](/2.5/extensions/task-lists/)

Or, if you only want a subset of GFM extensions, you can add them individually like this instead:

```php
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\DisallowedRawHtml\DisallowedRawHtmlExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\TaskList\TaskListExtension;
use League\CommonMark\MarkdownConverter;

// Define your configuration, if needed
$config = [];

// Configure the Environment with all the CommonMark parsers/renderers
$environment = new Environment($config);
$environment->addExtension(new CommonMarkCoreExtension());

// Remove any of the lines below if you don't want a particular feature
$environment->addExtension(new AutolinkExtension());
$environment->addExtension(new DisallowedRawHtmlExtension());
$environment->addExtension(new StrikethroughExtension());
$environment->addExtension(new TableExtension());
$environment->addExtension(new TaskListExtension());

$converter = new MarkdownConverter($environment);
echo $converter->convert('Hello GFM!');
```

This extension relies on the `CommonMarkCoreExtension` being enabled, so [don't forget to include that](/2.5/extensions/commonmark/) too.
