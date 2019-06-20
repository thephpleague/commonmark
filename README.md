# league/commonmark

[![Latest Version](https://img.shields.io/packagist/v/league/commonmark.svg?style=flat-square)](https://packagist.org/packages/league/commonmark)
[![Total Downloads](https://img.shields.io/packagist/dt/league/commonmark.svg?style=flat-square)](https://packagist.org/packages/league/commonmark)
[![Software License](https://img.shields.io/badge/License-BSD--3-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/travis/thephpleague/commonmark/master.svg?style=flat-square)](https://travis-ci.org/thephpleague/commonmark)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/thephpleague/commonmark.svg?style=flat-square)](https://scrutinizer-ci.com/g/thephpleague/commonmark/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/thephpleague/commonmark.svg?style=flat-square)](https://scrutinizer-ci.com/g/thephpleague/commonmark)
[![CII Best Practices](https://bestpractices.coreinfrastructure.org/projects/126/badge)](https://bestpractices.coreinfrastructure.org/projects/126)

[![Support development with Patreon](https://img.shields.io/badge/patreon-donate-red.svg)](https://www.patreon.com/colinodell)

**league/commonmark** is a highly-extensible PHP Markdown parser created by [Colin O'Dell][@colinodell] which supports the full [CommonMark] spec.  It is based on the [CommonMark JS reference implementation][commonmark.js] by [John MacFarlane] \([@jgm]\).

## 📦 Installation & Basic Usage

This project can be installed via [Composer]:

``` bash
$ composer require league/commonmark:^0.19
```

The `CommonMarkConverter` class provides a simple wrapper for converting CommonMark to HTML:

```php
use League\CommonMark\CommonMarkConverter;

$converter = new CommonMarkConverter([
    'html_input' => 'strip',
    'allow_unsafe_links' => false,
]);

echo $converter->convertToHtml('# Hello World!');

// <h1>Hello World!</h1>
```

Please note that only UTF-8 and ASCII encodings are supported.  If your Markdown uses a different encoding please convert it to UTF-8 before running it through this library.

🔒 If you will be parsing untrusted input from users, please consider setting the `html_input` and `allow_unsafe_links` options per the example above. See <https://commonmark.thephpleague.com/security/> for more details. If you also do choose to allow raw HTML input from untrusted users, considering using a library (like [HTML Purifier](https://github.com/ezyang/htmlpurifier)) to provide additional HTML filtering.

## 📓 Documentation

Full documentation on advanced usage, configuration, and customization can be found at [commonmark.thephpleague.com][docs].

## ⏫ Upgrading

Information on how to upgrade to newer versions of this library can be found at <https://commonmark.thephpleague.com/releases>.

## 🗃️ Related Packages

### Integrations

- [CakePHP 3](https://github.com/gourmet/common-mark)
- [Drupal 7 & 8](https://www.drupal.org/project/markdown)
- [Laravel 4 & 5](https://github.com/GrahamCampbell/Laravel-Markdown)
- [Sculpin](https://github.com/bcremer/sculpin-commonmark-bundle)
- [Symfony 2 & 3](https://github.com/webuni/commonmark-bundle)
- [Symfony 4](https://github.com/avensome/commonmark-bundle)
- [Twig Markdown extension](https://github.com/twigphp/markdown-extension)
- [Twig filter and tag](https://github.com/aptoma/twig-markdown)

### League Extensions

The PHP League offers useful extensions that add extra syntax and features:

 - [`league/commonmark-extras`](https://github.com/thephpleague/commonmark-extras) - Bundles the extensions below into a single dependency for convenience
   - [`league/commonmark-ext-autolink`](https://github.com/thephpleague/commonmark-ext-autolink) - Extension for league/commonmark which autolinks URLs, emails, and (optionally) @-mentions
   - [`league/commonmark-ext-smartpunct`](https://github.com/thephpleague/commonmark-ext-smartpunct) - Intelligently converts ASCII quotes, dashes, and ellipses to their Unicode equivalents
   - [`league/commonmark-ext-strikethrough`](https://github.com/thephpleague/commonmark-ext-strikethrough) - Adds support for `~~strikethrough~~` syntax
   - [`league/commonmark-ext-task-list`]()(https://github.com/thephpleague/commonmark-ext-task-list) - Support for Github-style task lists
 - [`league/commonmark-ext-inlines-only`](https://github.com/thephpleague/commonmark-ext-inlines-only) - Renders inline text without paragraph tags or other block-level elements
 - [`league/commonmark-ext-external-link`](https://github.com/thephpleague/commonmark-ext-external-link) - Mark external links, make them open in new windows, etc.

You can add them to your project or use them as examples to [develop your own custom features](https://commonmark.thephpleague.com/customization/overview/).

### Community Extensions

Custom parsers/renderers can be bundled into extensions which extend CommonMark.  Here are some that you may find interesting:

 - [CommonMark Table Extension](https://github.com/webuni/commonmark-table-extension) - Adds the ability to create tables in CommonMark documents.
 - [CommonMark Attributes Extension](https://github.com/webuni/commonmark-attributes-extension) - Adds a syntax to define attributes on the various HTML elements.
 - [Alt Three Emoji](https://github.com/AltThree/Emoji) An emoji parser for CommonMark.
 - [Sup Sub extensions](https://github.com/OWS/commonmark-sup-sub-extensions) - Adds support of superscript and subscript (`<sup>` and `<sub>` HTML tags)

Others can be found on [Packagist under the `commonmark-extension` package type](https://packagist.org/packages/league/commonmark?type=commonmark-extension).

If you build your own, feel free to submit a PR to add it to this list!

### Others

Check out the other cool things people are doing with `league/commonmark`: <https://packagist.org/packages/league/commonmark/dependents>

## 🏷️ Versioning

[SemVer](http://semver.org/) is followed closely.  0.x.0 versions will introduce breaking changes to the codebase, so be careful which version constraints you use. **It's highly recommended that you use [Composer's caret operator](https://getcomposer.org/doc/articles/versions.md#caret) to ensure compatibility**; for example: `^0.18`.  This is equivalent to `>=0.18.0 <0.19.0`.

0.x.y releases should not introduce breaking changes to the codebase; however, they might change the resulting AST or HTML output of parsed Markdown (due to bug fixes, spec changes, etc.)  As a result, you might get slightly different HTML, but any custom code built onto this library should still function correctly.

If you're only using the `CommonMarkConverter` class or `ConverterInterface` to convert Markdown (no other class references, custom parsers, etc.), then it should be safe to use a broader constraint like `~0.18`, `>0.18`, etc.  I personally promise to never break this specific class in any future 0.x or 1.0 release.

## 👷‍♀️ Contributing

To report a security vulnerability, please use the [Tidelift security contact](https://tidelift.com/security). Tidelift will coordinate the fix and disclosure with us.

If you encounter a bug in the spec, please report it to the [CommonMark] project.  Any resulting fix will eventually be implemented in this project as well.

Contributions to this library are **welcome**, especially ones that:

 * Improve usability or flexibility without compromising our ability to adhere to the [CommonMark spec]
 * Mirror fixes made to the [reference implementation][commonmark.js]
 * Optimize performance
 * Fix issues with adhering to the [CommonMark spec]

Major refactoring to core parsing logic should be avoided if possible so that we can easily follow updates made to [the reference implementation][commonmark.js]. That being said, we will absolutely consider changes which don't deviate too far from the reference spec or which are favored by other popular CommonMark implementations.

Please see [CONTRIBUTING](https://github.com/thephpleague/commonmark/blob/master/.github/CONTRIBUTING.md) for additional details.

## 🧪 Testing

``` bash
$ composer test
```

This will also test league/commonmark against the latest supported spec.

## 🚀 Performance Benchmarks

You can compare the performance of **league/commonmark** to other popular parsers by running the included benchmark tool:

``` bash
$ ./tests/benchmark/benchmark.php
```

## 👥 Credits & Acknowledgements

- [Colin O'Dell][@colinodell]
- [John MacFarlane][@jgm]
- [All Contributors]

This code is partially based on the [CommonMark JS reference implementation][commonmark.js] which is written, maintained and copyrighted by [John MacFarlane].  This project simply wouldn't exist without his work.

### Sponsors

We'd also like to extend our sincere thanks the following sponsors who support ongoing development of this project:

 - [RIPS Technologies](https://www.ripstech.com/) for supporting this project with a complimentary [RIPS SaaS](https://www.ripstech.com/product/) license
 - [JetBrains](https://www.jetbrains.com/) for supporting this project with complimentary [PhpStorm](https://www.jetbrains.com/phpstorm/) licenses

Are you interested in sponsoring development of this project? [Make a pledge](https://www.patreon.com/join/colinodell) of $10 or more and we'll include your name [on our website](https://commonmark.thephpleague.com/#sponsors)!

## 📄 License

**league/commonmark** is licensed under the BSD-3 license.  See the [`LICENSE`](LICENSE) file for more details.

## 🏛️ Governance

This project is primarily maintained by [Colin O'Dell][@colinodell].  Members of the [PHP League] Leadership Team may occasionally assist with some of these duties.

[CommonMark]: http://commonmark.org/
[CommonMark spec]: http://spec.commonmark.org/
[commonmark.js]: https://github.com/jgm/commonmark.js
[John MacFarlane]: http://johnmacfarlane.net
[docs]: https://commonmark.thephpleague.com/
[docs-examples]: https://commonmark.thephpleague.com/customization/overview/#examples
[docs-example-twitter]: https://commonmark.thephpleague.com/customization/inline-parsing#example-1---twitter-handles
[docs-example-smilies]: https://commonmark.thephpleague.com/customization/inline-parsing#example-2---emoticons
[All Contributors]: https://github.com/thephpleague/commonmark/contributors
[@colinodell]: https://www.twitter.com/colinodell
[@jgm]: https://github.com/jgm
[jgm/stmd]: https://github.com/jgm/stmd
[Composer]: https://getcomposer.org/
[PHP League]: https://thephpleague.com
