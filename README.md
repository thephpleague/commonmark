CommonMark Table Extension
==========================

[![Latest Version](https://img.shields.io/packagist/v/league/commonmark-ext-table.svg?style=flat-square)](https://packagist.org/packages/league/commonmark-ext-table)
[![Build Status](https://img.shields.io/travis/thephpleague/commonmark-ext-table.svg?style=flat-square)](https://travis-ci.org/thephpleague/commonmark-ext-table)
[![Code Quality](https://img.shields.io/scrutinizer/g/thephpleague/commonmark-ext-table.svg?style=flat-square)](https://scrutinizer-ci.com/g/thephpleague/commonmark-ext-table/code-structure)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/thephpleague/commonmark-ext-table.svg?style=flat-square)](https://scrutinizer-ci.com/g/thephpleague/commonmark-ext-table)

The Table extension adds the ability to create tables in CommonMark documents.

Installation
------------

This project can be installed via Composer:

    composer require league/commonmark-ext-table

Usage
-----

Configure your `Environment` as usual and simply add the `TableExtension` provided by this package:

```php
use League\CommonMark\Converter;
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;
use League\CommonMark\Ext\Table\TableExtension;

// Obtain a pre-configured Environment with all the standard CommonMark parsers/renderers ready-to-go
$environment = Environment::createCommonMarkEnvironment();

// Add this extension
$environment->addExtension(new TableExtension());

// Instantiate the converter engine and start converting some Markdown!
$converter = new Converter(new DocParser($environment), new HtmlRenderer($environment));

echo $converter->convertToHtml('# Hello World!');
```

Syntax
------

This package is fully compatible with GFM-style tables:

### Simple

Code:
```markdown
th | th(center) | th(right)
---|:----------:|----------:
td | td         | td
```

Result:
```html
<table>
<thead>
<tr>
<th style="text-align: left">th</th>
<th style="text-align: center">th(center)</th>
<th style="text-align: right">th(right<)/th>
</tr>
</thead>
<tbody>
<tr>
<td style="text-align: left">td</td>
<td style="text-align: center">td</td>
<td style="text-align: right">td</td>
</tr>
</tbody>
</table>
```

### Advanced

```markdown
| header 1 | header 2 | header 2 |
| :------- | :------: | -------: |
| cell 1.1 | cell 1.2 | cell 1.3 |
| cell 2.1 | cell 2.2 | cell 2.3 |
```

### Table caption

```markdown
header 1 | header 2
-------- | --------
cell 1.1 | cell 1.2
[Simple table]
```

Code:
```markdown
header 1 | header 2
-------- | --------
cell 1.1 | cell 1.2
[*Prototype* table][reference_table]
```

Result:
```html
<table>
<caption id="reference_table"><em>Prototype</em> table</caption>
<thead>
<tr>
<th>header 1</th>
<th>header 2</th>
</tr>
</thead>
<tbody>
<tr>
<td>cell 1.1</td>
<td>cell 1.2</td>
</tr>
</tbody>
</table>
<table>
```

Changelog
---------

Please refer to the [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

Development
-----------

You need to have *php* or *docker* installed to develop the library. To list all available commands run:

```bash
./run
```

Security
--------

If you discover any security related issues, please email colinodell@gmail.com instead of using the issue tracker.

Credits
-------

- [Martin Hasoň](https://github.com/hason)
- [Webuni s.r.o.](https://www.webuni.cz)
- [Colin O'Dell](https://github.com/colinodell)
- [All Contributors](../../contributors)

License
-------

This library is licensed under the MIT license.  See the [License File](LICENSE) for more information.
