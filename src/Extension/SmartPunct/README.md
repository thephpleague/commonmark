# Smart Punctuation for `league/commonmark`

Intelligently converts ASCII quotes, dashes, and ellipses to their Unicode equivalents.  For use with the [`league/commonmark` Markdown parser for PHP](https://github.com/thephpleague/commonmark).

For example, this Markdown...

```md
"CommonMark is the PHP League's Markdown parser," she said.  "It's super-configurable... you can even use additional extensions to expand its capabilities -- just like this one!"
```

Will result in this HTML:

```html
<p>“CommonMark is the PHP League’s Markdown parser,” she said.  “It’s super-configurable… you can even use additional extensions to expand its capabilities – just like this one!”</p>
```

## Usage

Extensions can be added to any new `Environment`:

``` php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\SmartPunct\SmartPunctExtension;

// Obtain a pre-configured Environment with all the CommonMark parsers/renderers ready-to-go
$environment = Environment::createCommonMarkEnvironment();

// Add this extension
$environment->addExtension(new SmartPunctExtension());

// Set your configuration
$config = [
    'smartpunct' => [
        'double_quote_opener' => '“',
        'double_quote_closer' => '”',
        'single_quote_opener' => '‘',
        'single_quote_closer' => '’',
    ],
];

// Instantiate the converter engine and start converting some Markdown!
$converter = new CommonMarkConverter($config, $environment);
echo $converter->convertToHtml('# Hello World!');
```

[link-league-commonmark]: https://github.com/thephpleague/commonmark
