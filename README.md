# commonmark-php #

[![Latest Stable Version](https://poser.pugx.org/colinodell/commonmark-php/v/stable.svg)](https://packagist.org/packages/colinodell/commonmark-php)
[![Total Downloads](https://poser.pugx.org/colinodell/commonmark-php/downloads.svg)](https://packagist.org/packages/colinodell/commonmark-php)
[![Latest Unstable Version](https://poser.pugx.org/colinodell/commonmark-php/v/unstable.svg)](https://packagist.org/packages/colinodell/commonmark-php)
[![License](https://poser.pugx.org/colinodell/commonmark-php/license.svg)](https://packagist.org/packages/colinodell/commonmark-php)

[![Build Status](https://travis-ci.org/colinodell/commonmark-php.svg?branch=master)](https://travis-ci.org/colinodell/commonmark-php)
[![Coverage Status](https://coveralls.io/repos/colinodell/commonmark-php/badge.png?branch=master)](https://coveralls.io/r/colinodell/commonmark-php?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/5411a6c84cd160cf2c000263/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5411a6c84cd160cf2c000263)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/6250954a-f9e8-4e49-bb17-ec24b006e33b/mini.png)](https://insight.sensiolabs.com/projects/6250954a-f9e8-4e49-bb17-ec24b006e33b)

**commonmark-php** is a Markdown parser for PHP which supports the full [CommonMark] spec.  It is directly based on [stmd.js] by [John MacFarlane] \([@jgm]\).

## Installation ##

This project can be installed via [Composer]:

    {
        "require": {
            "colinodell/commonmark-php": "dev-master"
        }
    }

## Usage ##

The `CommonMark` class provides a simple wrapper for converting CommonMark to HTML:

```php
use ColinODell\CommonMark\CommonMarkConverter;

$converter = new CommonMarkConverter();
echo $converter->convertToHtml('# Hello World!');

// <h1>Hello World!</h1>
```

The actual conversion process requires two steps:

 1. Parsing the Markdown input into an AST
 2. Rendering the AST document as HTML

You can do this yourself if you wish:

```php
use ColinODell\CommonMark\DocParser;
use ColinODell\CommonMark\HtmlRenderer;

$parser = new DocParser();
$htmlRenderer = new HtmlRenderer();

$markdown = '# Hello World!';

$document = $parser->parse($markdown);
echo $htmlRenderer ->render($document);

// <h1>Hello World!</h1>
```

## Compatibility with CommonMark ##

This project aims to fully support the entire [CommonMark spec] - other flavors of Markdown may work but are not supported.  Any/all changes made to the [spec][CommonMark spec] or [stmd.js] parser should eventually find their way back into this codebase.

This is **not** part of CommonMark, but rather a compatible derivative.

## Performance Benchmarks ##

You can compare the performance of **commonmark-php** to other popular parsers by running the `tests/benchmark/benchmark.php` tool.

As of version 0.1.1, this parser matches the performance of PHP Markdown Extra.

## Stability and Versioning ##

While this package works well, the underlying code should not be considered "stable" yet.  The original spec and JS parser may undergo changes in the near future, which will result in corresponding changes to this code.  Any methods tagged with `@api` are not expected to change, but other methods/classes might.

Major release 1.0.0 will be reserved for when both CommonMark and this project are considered stable. 0.x.x will be used until that happens.

## Contributing ##

If you encounter a bug in the spec, please report it to the [jgm/stmd] project.  Any resulting fix will eventually be implemented in this project as well.

For now, I'd like to maintain similar logic as the [stmd.js] parser until everything is stable.  I'll gladly accept any contributions which:

 * Mirror fixes made to the [jgm/stmd] project
 * Optimize existing methods or regular expressions
 * Fix issues with adhering to the spec examples

Major refactoring should be avoided for now so that we can easily follow updates made to [jgm/stmd].  This restriction will likely be lifted once the CommonMark specs and implementations are considered stable.

## Credits & Acknowledgements ##

This code is a port of [stmd.js] which is written, maintained and copyrighted by [John MacFarlane].  This project simply wouldn't exist without his work.

## License ##

**commonmark-php** is licensed under the BSD-3 license.  See the `LICENSE` file for more details.

[CommonMark]: http://commonmark.org/
[CommonMark spec]: http://spec.commonmark.org/
[stmd.js]: https://github.com/jgm/stmd/blob/master/js/stmd.js
[John MacFarlane]: http://johnmacfarlane.net
[@jgm]: https://github.com/jgm
[jgm/stmd]: https://github.com/jgm/stmd
[Composer]: https://getcomposer.org/
