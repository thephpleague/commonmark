# Smart Punctuation for `league/commonmark`

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Intelligently converts ASCII quotes, dashes, and ellipses to their Unicode equivalents.

For example, this Markdown...

```md
"CommonMark is the PHP League's Markdown parser," she said.  "It's super-configurable... you can even use additional extensions to expand its capabilities -- just like this one!"
```

Will result in this HTML:

```html
<p>“CommonMark is the PHP League’s Markdown parser,” she said.  “It’s super-configurable… you can even use additional extensions to expand its capabilities – just like this one!”</p>
```

## Install

Via Composer

``` bash
$ composer require league/commonmark-ext-smartpunct
```

## Usage

Extensions can be added to any new `Environment`:

``` php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Ext\SmartPunct\SmartPunctExtension;

// Obtain a pre-configured Environment with all the CommonMark parsers/renderers ready-to-go
$environment = Environment::createCommonMarkEnvironment();

// Add this extension
$environment->addExtension(new SmartPunctExtension());

// Instantiate the converter engine and start converting some Markdown!
$converter = new CommonMarkConverter($config, $environment);
echo $converter->convertToHtml('# Hello World!');
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

[ico-version]: https://img.shields.io/packagist/v/league/commonmark-ext-smartpunct.svg?style=flat-square
[ico-license]: http://img.shields.io/badge/License-BSD--3-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/thephpleague/commonmark-ext-smartpunct/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/thephpleague/commonmark-ext-smartpunct.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/thephpleague/commonmark-ext-smartpunct.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/league/commonmark-ext-smartpunct.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/league/commonmark-ext-smartpunct
[link-travis]: https://travis-ci.org/thephpleague/commonmark-ext-smartpunct
[link-scrutinizer]: https://scrutinizer-ci.com/g/thephpleague/commonmark-ext-smartpunct/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/thephpleague/commonmark-ext-smartpunct
[link-downloads]: https://packagist.org/packages/league/commonmark-ext-smartpunct
[link-author]: https://github.com/colinodell
[link-contributors]: ../../contributors
[link-league-commonmark]: https://github.com/thephpleague/commonmark
[link-jgm]: https://github.com/jgm
