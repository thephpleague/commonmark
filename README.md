# league/commonmark

[![Latest Version](https://img.shields.io/packagist/v/league/commonmark.svg?style=flat-square)](https://packagist.org/packages/league/commonmark)
[![Total Downloads](https://img.shields.io/packagist/dt/league/commonmark.svg?style=flat-square)](https://packagist.org/packages/league/commonmark)
[![Software License](https://img.shields.io/badge/License-BSD--3-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/travis/thephpleague/commonmark/master.svg?style=flat-square)](https://travis-ci.org/thephpleague/commonmark)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/thephpleague/commonmark.svg?style=flat-square)](https://scrutinizer-ci.com/g/thephpleague/commonmark/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/thephpleague/commonmark.svg?style=flat-square)](https://scrutinizer-ci.com/g/thephpleague/commonmark)
[![CII Best Practices](https://bestpractices.coreinfrastructure.org/projects/126/badge)](https://bestpractices.coreinfrastructure.org/projects/126)

[![Support development with Patreon](https://img.shields.io/badge/patreon-donate-red.svg)](https://www.patreon.com/colinodell)

![league/commonmark](commonmark-banner.png)

**league/commonmark** is a highly-extensible PHP Markdown parser created by [Colin O'Dell][@colinodell] which supports the full [CommonMark] spec.  It is based on the [CommonMark JS reference implementation][commonmark.js] by [John MacFarlane] \([@jgm]\).

## 📦 Installation & Basic Usage

This project can be installed via [Composer]:

``` bash
$ composer require league/commonmark
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

### GFM Extensions

You can easily add support for Github-Flavored Markdown by installing the [`league/commonmark-extras`](https://github.com/thephpleague/commonmark-extras) package, which includes bundles all of the extensions listed below:

| Feature | Package Name | Description |
| ------- | ------------ | ----------- |
| Autolinks | [`league/commonmark-ext-autolink`](https://github.com/thephpleague/commonmark-ext-autolink) | Automatically links URLs, emails, and (optionally) @-mentions without needing to use `<...>` |
| Smart Punctuation | [`league/commonmark-ext-smartpunct`](https://github.com/thephpleague/commonmark-ext-smartpunct) | Intelligently converts ASCII quotes, dashes, and ellipses to their Unicode equivalents |
| Strikethrough | [`league/commonmark-ext-strikethrough`](https://github.com/thephpleague/commonmark-ext-strikethrough) | Adds support for `~~strikethrough~~` syntax |
| Task Lists | [`league/commonmark-ext-task-list`](https://github.com/thephpleague/commonmark-ext-task-list) | Support for Github-style task lists |
| Tables | [`league/commonmark-ext-table`](https://github.com/thephpleague/commonmark-ext-table) | GFM-style tables |

### Other PHP League Extensions

 - [`league/commonmark-ext-inlines-only`](https://github.com/thephpleague/commonmark-ext-inlines-only) - Renders inline text without paragraph tags or other block-level elements
 - [`league/commonmark-ext-external-link`](https://github.com/thephpleague/commonmark-ext-external-link) - Mark external links, make them open in new windows, etc.

You can add them to your project or use them as examples to [develop your own custom features](https://commonmark.thephpleague.com/customization/overview/).

### Community Extensions

Custom parsers/renderers can be bundled into extensions which extend CommonMark.  Here are some that you may find interesting:

 - [CommonMark Attributes Extension](https://github.com/webuni/commonmark-attributes-extension) - Adds a syntax to define attributes on the various HTML elements.
 - [Alt Three Emoji](https://github.com/AltThree/Emoji) An emoji parser for CommonMark.
 - [Sup Sub extensions](https://github.com/OWS/commonmark-sup-sub-extensions) - Adds support of superscript and subscript (`<sup>` and `<sub>` HTML tags)

Others can be found on [Packagist under the `commonmark-extension` package type](https://packagist.org/packages/league/commonmark?type=commonmark-extension).

If you build your own, feel free to submit a PR to add it to this list!

### Others

Check out the other cool things people are doing with `league/commonmark`: <https://packagist.org/packages/league/commonmark/dependents>

## 🏷️ Versioning

[SemVer](http://semver.org/) is followed closely. Minor and patch releases should not introduce breaking changes to the codebase; however, they might change the resulting AST or HTML output of parsed Markdown (due to bug fixes, spec changes, etc.)  As a result, you might get slightly different HTML, but any custom code built onto this library should still function correctly.

Any classes or methods marked `@internal` are not intended for use outside of this library and are subject to breaking changes at any time, so please avoid using them.

## 🛠️ Maintenance & Support

When a new **minor** version (`1.x`) is released, the previous one will continue to receive security and bug fixes for *at least* 3 months.

When a new **major** version is released (`1.0`, `2.0`, etc), the previous one (`0.19.x`) will receive bug fixes for *at least* 3 months and security updates for 6 months after that new release comes out.

(This policy may change in the future and exceptions may be made on a case-by-case basis.)

**Professional support, including notification of new releases and security updates, is available through a [Tidelift Subscription](https://tidelift.com/subscription/pkg/packagist-league-commonmark?utm_source=packagist-league-commonmark&utm_medium=referral&utm_campaign=readme).**

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

 - [Tidelift](https://tidelift.com/subscription/pkg/packagist-league-commonmark?utm_source=packagist-league-commonmark&utm_medium=referral&utm_campaign=readme) for offering support to both the maintainers and end-users through their [professional support](https://tidelift.com/subscription/pkg/packagist-league-commonmark?utm_source=packagist-league-commonmark&utm_medium=referral&utm_campaign=readme) program
 - [RIPS Technologies](https://www.ripstech.com/) for supporting this project with a complimentary [RIPS SaaS](https://www.ripstech.com/product/) license
 - [JetBrains](https://www.jetbrains.com/) for supporting this project with complimentary [PhpStorm](https://www.jetbrains.com/phpstorm/) licenses

Are you interested in sponsoring development of this project? [Make a pledge](https://www.patreon.com/join/colinodell) of $10 or more and we'll include your name [on our website](https://commonmark.thephpleague.com/#sponsors)!

## 📄 License

**league/commonmark** is licensed under the BSD-3 license.  See the [`LICENSE`](LICENSE) file for more details.

## 🏛️ Governance

This project is primarily maintained by [Colin O'Dell][@colinodell].  Members of the [PHP League] Leadership Team may occasionally assist with some of these duties.

---

<div align="center">
	<b>
		<a href="https://tidelift.com/subscription/pkg/packagist-league-commonmark?utm_source=packagist-league-commonmark&utm_medium=referral&utm_campaign=readme">Get professional support for league/commonmark with a Tidelift subscription</a>
	</b>
	<br>
	<sub>
		Tidelift helps make open source sustainable for maintainers while giving companies<br>assurances about security, maintenance, and licensing for their dependencies.
	</sub>
</div>

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
