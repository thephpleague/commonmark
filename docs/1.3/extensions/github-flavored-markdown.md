---
layout: default
title: GitHub-Flavored Markdown
description: The GithubFlavoredMarkdownExtension class includes all the GFM addons
---

# GitHub-Flavored Markdown

You can manually add the GFM extension to your environment like this:

```php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;

$environment = Environment::createCommonMarkEnvironment();
$environment->addExtension(new GithubFlavoredMarkdownExtension());

$converter = new CommonMarkConverter([], $environment);
echo $converter->convertToHtml('Hello GFM!');
```

This will automatically include all of these sub-extensions/features for you:

- [Autolinks](/1.3/extensions/autolinks/)
- [Disallowed Raw HTML](/1.3/extensions/disallowed-raw-html/)
- [Strikethrough](/1.3/extensions/strikethrough/)
- [Tables](/1.3/extensions/tables/)
- [Task Lists](/1.3/extensions/task-lists/)

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

This extension relies on the `CommonMarkCoreExtension` being enabled, so [don't forget to include that](/1.3/extensions/commonmark/) too.
