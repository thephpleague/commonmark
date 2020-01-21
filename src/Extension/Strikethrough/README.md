# Strikethrough support for `league/commonmark`

This extension adds strikethrough Markdown support for the [league/commonmark](link-league-commonmark) PHP Markdown parsing engine, which itself is based on the CommonMark spec.

It allows users to use `~~` in order to indicate text that should be rendered within `<del>` tags.

## Usage

Extensions can be added to any new `Environment`:

```php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;

// Obtain a pre-configured Environment with all the CommonMark parsers/renderers ready-to-go
$environment = Environment::createCommonMarkEnvironment();

// Add this extension
$environment->addExtension(new StrikethroughExtension());

// Instantiate the converter engine and start converting some Markdown!
$converter = new CommonMarkConverter($config, $environment);
echo $converter->convertToHtml('This extension is ~~good~~ great!');
```

[link-league-commonmark]: https://github.com/thephpleague/commonmark
