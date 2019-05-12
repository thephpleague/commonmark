# GFM-style task list extension for `league/commonmark`

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

This extension adds [GFM-style task list items][link-gfm-spec-task-lists] to the [`league/commonmark` Markdown parser for PHP][link-league-commonmark].

## Install

Via Composer

``` bash
$ composer require league/commonmark-ext-task-list
```

## Usage

Configure your `Environment` as usual and simply add the `TaskListExtension` provided by this package:

```php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Ext\TaskList\TaskListExtension;

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

[ico-version]: https://img.shields.io/packagist/v/league/commonmark-ext-task-list.svg?style=flat-square
[ico-license]: http://img.shields.io/badge/License-BSD--3-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/thephpleague/commonmark-ext-task-list/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/thephpleague/commonmark-ext-task-list.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/thephpleague/commonmark-ext-task-list.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/league/commonmark-ext-task-list.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/league/commonmark-ext-task-list
[link-travis]: https://travis-ci.org/thephpleague/commonmark-ext-task-list
[link-scrutinizer]: https://scrutinizer-ci.com/g/thephpleague/commonmark-ext-task-list/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/thephpleague/commonmark-ext-task-list
[link-downloads]: https://packagist.org/packages/league/commonmark-ext-task-list
[link-author]: https://github.com/colinodell
[link-contributors]: ../../contributors
[link-league-commonmark]: https://github.com/thephpleague/commonmark
[link-gfm-spec-task-lists]: https://github.github.com/gfm/#task-list-items-extension-
