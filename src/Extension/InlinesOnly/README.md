# Inline-only extension for `league/commonmark`

This extension configures the [`league/commonmark` Markdown parser for PHP](https://github.com/thephpleague/commonmark) to only render inline elements - no paragraph tags, headers, code blocks, etc.

## Usage

Although you normally add extra extensions to the default core one, we're not going to do that here, because this is essentially a slimmed-down version of the core extension:

```php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\InlinesOnly\InlinesOnlyExtension;

// Create a new, empty environment
$environment = new Environment();

// Add this extension
$environment->addExtension(new InlinesOnlyExtension());

// Instantiate the converter engine and start converting some Markdown!
$converter = new CommonMarkConverter($config, $environment);
echo $converter->convertToHtml('**Hello World!**');
```

[link-league-commonmark]: https://github.com/thephpleague/commonmark
