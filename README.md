# URL and email autolinking extension for `league/commonmark`

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

This extension adds [GFM-style autolinking][link-gfm-spec-autolinking] to the [`league/commonmark` Markdown parser for PHP][link-league-commonmark].  It automatically link URLs and email addresses even when the CommonMark `<...>` autolink syntax is not used.

It also provides a parser to autolink `@mentions` to Twitter, Github, or any custom service you wish, though this is disabled by default.

## Install

Via Composer

``` bash
$ composer require league/commonmark-ext-autolink
```

## Usage

Configure your `Environment` as usual and simply add the `AutolinkExtension` provided by this package:

```php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Ext\Autolink\AutolinkExtension;

// Obtain a pre-configured Environment with all the CommonMark parsers/renderers ready-to-go
$environment = Environment::createCommonMarkEnvironment();

// Add this extension
$environment->addExtension(new AutolinkExtension());

// Instantiate the converter engine and start converting some Markdown!
$converter = new CommonMarkConverter([], $environment);
echo $converter->convertToHtml('I successfully installed the https://github.com/thephpleague/commonmark-ext-autolink extension!');
```

## `@mention` Autolinking

This extension also provides functionality to automatically link "mentions" like `@colinodell` to Twitter, Github, or any other site of your choice!

For Twitter:

```php
use League\CommonMark\Environment;
use League\CommonMark\Ext\Autolink\InlineMentionParser;

$environment = Environment::createCommonMarkEnvironment();
$environment->addInlineParser(InlineMentionParser::createTwitterHandleParser());

// TODO: Instantiate your converter and convert some Markdown
```

For GitHub:

```php
use League\CommonMark\Environment;
use League\CommonMark\Ext\Autolink\InlineMentionParser;

$environment = Environment::createCommonMarkEnvironment();
$environment->addInlineParser(InlineMentionParser::createGithubHandleParser());

// TODO: Instantiate your converter and convert some Markdown
```

Or configure your own custom one:

```php
use League\CommonMark\Environment;
use League\CommonMark\Ext\Autolink\InlineMentionParser;

$environment = Environment::createCommonMarkEnvironment();
$environment->addInlineParser(new InlineMentionParser('https://www.example.com/users/%s/profile'));

// TODO: Instantiate your converter and convert some Markdown
```

When creating your own, you can provide two parameters to the constructor:

 - A URL template where `%s` is replaced with the username (required)
 - A regular expression to parse and validate the username (optional - defaults to `'/^[A-Za-z0-9_]+(?!\w)/'`)

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

[ico-version]: https://img.shields.io/packagist/v/league/commonmark-ext-autolink.svg?style=flat-square
[ico-license]: http://img.shields.io/badge/License-BSD--3-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/thephpleague/commonmark-ext-autolink/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/thephpleague/commonmark-ext-autolink.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/thephpleague/commonmark-ext-autolink.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/league/commonmark-ext-autolink.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/league/commonmark-ext-autolink
[link-travis]: https://travis-ci.org/thephpleague/commonmark-ext-autolink
[link-scrutinizer]: https://scrutinizer-ci.com/g/thephpleague/commonmark-ext-autolink/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/thephpleague/commonmark-ext-autolink
[link-downloads]: https://packagist.org/packages/league/commonmark-ext-autolink
[link-author]: https://github.com/colinodell
[link-contributors]: ../../contributors
[link-league-commonmark]: https://github.com/thephpleague/commonmark
[link-gfm-spec-autolinking]: https://github.github.com/gfm/#autolinks-extension-
