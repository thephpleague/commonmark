# Inline-only extension for `league/commonmark`

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

This extension configures the [`league/commonmark` Markdown parser for PHP](https://github.com/thephpleague/commonmark) to only render inline elements - no paragraph tags, headers, code blocks, etc.

## Install

Via Composer

``` bash
$ composer require league/commonmark-ext-inlines-only
```

## Usage

Although you normally add extra extensions to the default core one, we're not going to do that here, because this is essentially a slimmed-down version of the core extension:

``` php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Ext\InlinesOnly\InlinesOnlyExtension;

// Create a new, empty environment
$environment = new Environment();

// Add this extension
$environment->addExtension(new InlinesOnlyExtension());

// Instantiate the converter engine and start converting some Markdown!
$converter = new CommonMarkConverter($config, $environment);
echo $converter->convertToHtml('**Hello World!**');
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
- [John MacFarlane][link-jgm]
- [All Contributors][link-contributors]

## License

This library is licensed under the BSD-3 license.  See the [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/league/commonmark-ext-inlines-only.svg?style=flat-square
[ico-license]: http://img.shields.io/badge/License-BSD--3-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/thephpleague/commonmark-ext-inlines-only/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/thephpleague/commonmark-ext-inlines-only.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/thephpleague/commonmark-ext-inlines-only.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/league/commonmark-ext-inlines-only.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/league/commonmark-ext-inlines-only
[link-travis]: https://travis-ci.org/thephpleague/commonmark-ext-inlines-only
[link-scrutinizer]: https://scrutinizer-ci.com/g/thephpleague/commonmark-ext-inlines-only/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/thephpleague/commonmark-ext-inlines-only
[link-downloads]: https://packagist.org/packages/league/commonmark-ext-inlines-only
[link-author]: https://github.com/colinodell
[link-contributors]: ../../contributors
[link-league-commonmark]: https://github.com/thephpleague/commonmark
[link-jgm]: https://github.com/jgm
