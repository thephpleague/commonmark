# Extension to denote external links for `league/commonmark`

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

This extension to the [`league/commonmark` PHP Markdown parser][link-league-commonmark] can detect links to external sites and adjust the markup accordingly:

 - Adds a `rel="noopener noreferrer"` attribute
 - Optionally adds any custom HTML classes

## Install

Via Composer

``` bash
$ composer require league/commonmark-ext-external-link
```

## Usage

Configure your `Environment` as usual and simply add the `ExternalLinkExtension` provided by this package:

```php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Ext\ExternalLink\ExternalLinkExtension;

// Obtain a pre-configured Environment with all the CommonMark parsers/renderers ready-to-go
$environment = Environment::createCommonMarkEnvironment();

// Add this extension
$environment->addExtension(new ExternalLinkExtension());

// Set your configuration
$config = [
    'external_link' => [
        'internal_hosts' => 'www.example.com',
        'open_in_new_window' => true,
        'html_class' => 'external-link',
    ],
];

// Instantiate the converter engine and start converting some Markdown!
$converter = new CommonMarkConverter($config, $environment);
echo $converter->convertToHtml('I successfully installed the https://github.com/thephpleague/commonmark-ext-external-link extension!');
```

## Configuration

This extension supports three configuration options under the `external_link` configuration:

### `internal_hosts`

This option defines a whitelist of hosts which are considered non-external and should not receive the external link treatment.

This can be a single host name, like `'example.com'`, which must match exactly.

If you need to match subdomains, use a regular expression like `'/(^|\.)example\.com$/'`.  Note that you must use `/` characters to delimit your regex.

This configuration option also accepts an array of multiple strings and/or regexes:

```php
$config = [
    'external_link' => [
        'internal_hosts' => ['foo.example.com', 'bar.example.com', '/(^|\.)google\.com$/],
    ],
];
```

By default, if this option is not provided, all links will be considered external.

### `open_in_new_window`

This option (which defaults to `false`) determines whether any external links should open in a new tab/window.

### `html_class`

This option allows you to provide a `string` containing one or more HTML classes that should be added to the external link `<a>` tags:  No classes are added by default.

## Advanced Rendering

When an external link is detected, the `ExternalLinkProcessor` will set the `external` data option on the `Link` node to either `true` or `false`.  You can therefore create a [custom link renderer](https://commonmark.thephpleague.com/customization/inline-rendering/) which checks this value and behaves accordingly: 

```php
class MyCustomLinkRenderer implements InlineRendererInterface
{

    /**
     * @param Link                     $inline
     * @param ElementRendererInterface $htmlRenderer
     *
     * @return HtmlElement
     */
    public function render(AbstractInline $inline, ElementRendererInterface $htmlRenderer)
    {
        if (!($inline instanceof Link)) {
            throw new \InvalidArgumentException('Incompatible inline type: ' . \get_class($inline));
        }

        if ($inline->getData('external')) {
            // This is an external link - render it accordingly
        } else {
            // This is an internal link
        }
        
        // ...
    }
}
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email colinodell@gmail.com instead of using the issue tracker.

## Credits

- [Colin O'Dell][link-author]
- [All Contributors][link-contributors]

## License

This library is licensed under the BSD-3 license.  See the [License File](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/league/commonmark-ext-external-link.svg?style=flat-square
[ico-license]: http://img.shields.io/badge/License-BSD--3-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/thephpleague/commonmark-ext-external-link/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/thephpleague/commonmark-ext-external-link.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/thephpleague/commonmark-ext-external-link.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/league/commonmark-ext-external-link.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/league/commonmark-ext-external-link
[link-travis]: https://travis-ci.org/thephpleague/commonmark-ext-external-link
[link-scrutinizer]: https://scrutinizer-ci.com/g/thephpleague/commonmark-ext-external-link/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/thephpleague/commonmark-ext-external-link
[link-downloads]: https://packagist.org/packages/league/commonmark-ext-external-link
[link-author]: https://github.com/colinodell
[link-contributors]: ../../contributors
[link-league-commonmark]: https://github.com/thephpleague/commonmark
