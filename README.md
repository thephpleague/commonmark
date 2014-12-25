# CommonMark

[![Latest Version](https://img.shields.io/packagist/v/league/commonmark.svg?style=flat-square)](https://packagist.org/packages/league/commonmark)
[![Software License](http://img.shields.io/badge/License-BSD--3-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/travis/thephpleague/commonmark/master.svg?style=flat-square)](https://travis-ci.org/thephpleague/commonmark)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/thephpleague/commonmark.svg?style=flat-square)](https://scrutinizer-ci.com/g/thephpleague/commonmark/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/thephpleague/commonmark.svg?style=flat-square)](https://scrutinizer-ci.com/g/thephpleague/commonmark)
[![Total Downloads](https://img.shields.io/packagist/dt/league/commonmark.svg?style=flat-square)](https://packagist.org/packages/league/commonmark)

**league/commonmark** is a Markdown parser for PHP which supports the full [CommonMark] spec.  It is directly based the [CommonMark JS reference implementation][stmd.js] by [John MacFarlane] \([@jgm]\).

## Goals

While other Markdown parsers focus on speed, or try to enable a wide range of flavors, this parser will 
strive to match the C and JavaScript implementations of CommonMark to make a logical and similar API.

We will always focus on CommonMark compliance over speed, but performance improvements will definitely 
happen during efforts to reach v1.0.0 and afterwards.

## Installation

This project can be installed via [Composer]:

``` bash
$ composer require league/commonmark
```

## Usage & Customization

The `CommonMarkConverter` class provides a simple wrapper for converting CommonMark to HTML:

```php
use League\CommonMark\CommonMarkConverter;

$converter = new CommonMarkConverter();
echo $converter->convertToHtml('# Hello World!');

// <h1>Hello World!</h1>
```

The actual conversion process requires two steps:

 1. Parsing the Markdown input into an AST
 2. Rendering the AST document as HTML

You can do this yourself if you wish:

```php
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;

$environment = Environment::createCommonMarkEnvironment();
$parser = new DocParser($environment);
$htmlRenderer = new HtmlRenderer($environment);

$markdown = '# Hello World!';

$documentAST = $parser->parse($markdown);
echo $htmlRenderer->renderBlock($documentAST);

// <h1>Hello World!</h1>
```

This approach allows you to access (and possibly modify) the AST before it's rendered.

You can add your own parsers and renderers by [registering them with the `Environment` class](http://commonmark.thephpleague.com/customization/environment/).

The [documentation][docs] contains additional details and some helpful examples to get you started.  You can also reference the core CommonMark parsers/renderers as they use the same functionality available to you.

## Compatibility with CommonMark ##

This project aims to fully support the entire [CommonMark spec]. Other flavors of Markdown may work but are not supported.  Any/all changes made to the [spec][CommonMark spec] or [JS reference implementation][stmd.js] should eventually find their way back into this codebase.

The following table shows which versions of league/commonmark are compatible with which version of the CommonMark spec:

<table>
    <thead>
        <tr>
            <th>league/commonmark</th>
            <th>CommonMark spec</th>
            <th>Notes</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>0.5.x</strong><br>0.4.0</td>
            <td><strong><a href="http://spec.commonmark.org/0.13/">0.13</a></strong></td>
            <td>current spec (as of Dec 15 '14)</td>
        </tr>
        <tr>
            <td>0.3.0</td>
            <td><a href="http://spec.commonmark.org/0.12/">0.12</a></td>
            <td></td>
        </tr>
        <tr>
            <td>0.2.x</td>
            <td><a href="http://spec.commonmark.org/0.10/">0.10</a></td>
            <td></td>
        </tr>
        <tr>
            <td>0.1.x</td>
            <td><a href="https://github.com/jgm/CommonMark/blob/2cf0750a7a507eded4cf3c9a48fd1f924d0ce538/spec.txt">0.01</a></td>
            <td></td>
        </tr>
    </tbody>
</table>

This package is **not** part of CommonMark, but rather a compatible derivative.

## Documentation

Documentation can be found at [commonmark.thephpleague.com][docs].

## Testing

``` bash
$ ./vendor/bin/phpunit
```

This will also test league/commonmark against the latest supported spec.

## Performance Benchmarks

You can compare the performance of **league/commonmark** to other popular parsers by running the included benchmark tool:
 
``` bash
$ ./tests/benchmark/benchmark.php
```

## Stability and Versioning

While this package does work well, the underlying code should not be considered "stable" yet.  The original spec and JS parser may undergo changes in the near future, which will result in corresponding changes to this code.  Any methods tagged with `@api` are not expected to change, but other methods/classes might.

Major release 1.0.0 will be reserved for when both CommonMark and this project are considered stable. 0.x.x will be used until that happens.

SemVer will be followed [closely](http://semver.org/).

## Contributing

If you encounter a bug in the spec, please report it to the [CommonMark] project.  Any resulting fix will eventually be implemented in this project as well.

For now, I'd like to maintain similar logic as the [JS reference implementation][stmd.js] until everything is stable.  I'll gladly accept any contributions which:

 * Mirror fixes made to the [reference implementation][stmd.js]
 * Optimize existing methods or regular expressions
 * Fix issues with adhering to the spec examples

Major refactoring should be avoided for now so that we can easily follow updates made to [the reference implementation][stmd.js].  This restriction will likely be lifted once the CommonMark specs and implementations are considered stable.

Please see [CONTRIBUTING](https://github.com/thephpleague/commonmark/blob/master/CONTRIBUTING.md) for additional details.

## Credits & Acknowledgements

- [Colin O'Dell][@colinodell]
- [John MacFarlane][@jgm]
- [All Contributors]

This code is a port of the [CommonMark JS reference implementation][stmd.js] which is written, maintained and copyrighted by [John MacFarlane].  This project simply wouldn't exist without his work.

## License ##

**league/commonmark** is licensed under the BSD-3 license.  See the `LICENSE` file for more details.

[CommonMark]: http://commonmark.org/
[CommonMark spec]: http://spec.commonmark.org/
[stmd.js]: https://github.com/jgm/CommonMark/tree/master/js
[John MacFarlane]: http://johnmacfarlane.net
[docs]: http://commonmark.thephpleague.com/
[All Contributors]: https://github.com/thephpleague/commonmark/contributors
[@colinodell]: https://github.com/colinodell
[@jgm]: https://github.com/jgm
[jgm/stmd]: https://github.com/jgm/stmd
[Composer]: https://getcomposer.org/
