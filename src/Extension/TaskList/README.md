# GFM-style task list extension for `league/commonmark`

This extension adds [GFM-style task list items][link-gfm-spec-task-lists] to the [`league/commonmark` Markdown parser for PHP][link-league-commonmark].

## Usage

Configure your `Environment` as usual and simply add the `TaskListExtension` provided by this package:

```php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\TaskList\TaskListExtension;

// Obtain a pre-configured Environment with all the CommonMark parsers/renderers ready-to-go
$environment = Environment::createCommonMarkEnvironment();

// Add this extension
$environment->addExtension(new TaskListExtension());

// Instantiate the converter engine and start converting some Markdown!
$converter = new CommonMarkConverter([], $environment);

$markdown = <<<EOT
 - [x] Install this extension
 - [ ] ???
 - [ ] Profit!
EOT;

echo $converter->convertToHtml($markdown);
```

[link-league-commonmark]: https://github.com/thephpleague/commonmark
[link-gfm-spec-task-lists]: https://github.github.com/gfm/#task-list-items-extension-
