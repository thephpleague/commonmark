# Strikethrough support for `league/commonmark`

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

This extension adds strikethrough Markdown support for the [league/commonmark](link-league-commonmark) PHP Markdown parsing engine, which itself is based on the CommonMark spec.

It allows users to use `~~` in order to indicate text that should be rendered within `<del>` tags.

## Installation

This project can be installed via Composer:

``` bash
$ composer require league/commonmark-ext-strikethrough
```

## Usage

Extensions can be added to any new `Environment`:

``` php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Ext\Strikethrough\StrikethroughExtension;

// Obtain a pre-configured Environment with all the CommonMark parsers/renderers ready-to-go
$environment = Environment::createCommonMarkEnvironment();

// Add this extension
$environment->addExtension(new StrikethroughExtension());

// Instantiate the converter engine and start converting some Markdown!
$converter = new CommonMarkConverter($config, $environment);
echo $converter->convertToHtml('This extension is ~~good~~ great!');
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

- [Colin O'Dell][link-colinodell]
- [uAfrica Technologies (Pty) Ltd][link-uafrica]
- [All Contributors][link-contributors]

## License

This library is licensed under the MIT license.  See the `LICENSE` file for more information.

[ico-version]: https://img.shields.io/packagist/v/league/commonmark-ext-strikethrough.svg?style=flat-square
[ico-license]: http://img.shields.io/badge/License-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/thephpleague/commonmark-ext-strikethrough/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/thephpleague/commonmark-ext-strikethrough.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/thephpleague/commonmark-ext-strikethrough.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/league/commonmark-ext-strikethrough.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/league/commonmark-ext-strikethrough
[link-travis]: https://travis-ci.org/thephpleague/commonmark-ext-strikethrough
[link-scrutinizer]: https://scrutinizer-ci.com/g/thephpleague/commonmark-ext-strikethrough/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/thephpleague/commonmark-ext-strikethrough
[link-downloads]: https://packagist.org/packages/league/commonmark-ext-strikethrough
[link-uafrica]: https://github.com/uafrica
[link-colinodell]: https://github.com/colinodell
[link-contributors]: ../../contributors
[link-league-commonmark]: https://github.com/thephpleague/commonmark
