# uafrica/commonmark-ext

[![Latest Version](https://img.shields.io/packagist/v/uafrica/commonmark-ext.svg?style=flat-square)](https://packagist.org/packages/uafrica/commonmark-ext)
[![Software License](http://img.shields.io/badge/License-MIT-brightgreen.svg?style=flat-square)](LICENSE)

**uafrica/commonmark-ext** is a collection of custom parsers and renderers for the [league-commonmark] Markdown
parsing engine, which itself is based on the CommonMark spec.

## Current Custom Parsers and Renderers
* **Strikethrough:** Parser and Renderer. Allows users to use `~~` in order to indicate text that should be rendered within `<del>` tags.

## Installation

This project can be installed via [Composer]:

``` bash
$ composer require uafrica/commonmark-ext
```

## Testing

``` bash
$ ./vendor/bin/phpunit
```

## License

**uafrica/commonmark-ext** is licensed under the MIT.  See the `LICENSE` file for more details.

[league-commonmark]: https://github.com/thephpleague/commonmark
