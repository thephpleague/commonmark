CommonMark Table Extension
==========================

[![Latest Version](https://img.shields.io/packagist/v/league/commonmark-ext-table.svg?style=flat-square)](https://packagist.org/packages/league/commonmark-ext-table)
[![Build Status](https://img.shields.io/travis/league/commonmark-ext-table.svg?style=flat-square)](https://travis-ci.org/league/commonmark-ext-table)
[![Code Quality](https://img.shields.io/scrutinizer/g/league/commonmark-ext-table.svg?style=flat-square)](https://scrutinizer-ci.com/g/league/commonmark-ext-table/code-structure)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/league/commonmark-ext-table.svg?style=flat-square)](https://scrutinizer-ci.com/g/league/commonmark-ext-table)

The Table extension adds the ability to create tables in CommonMark documents.

Installation
------------

This project can be installed via Composer:

    composer require league/commonmark-ext-table

Usage
-----

```php
use League\CommonMark\Converter;
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;
use Webuni\CommonMark\TableExtension\TableExtension;

$environment = Environment::createCommonMarkEnvironment();
$environment->addExtension(new TableExtension());

$converter = new Converter(new DocParser($environment), new HtmlRenderer($environment));

echo $converter->convertToHtml('# Hello World!');
```

Syntax
------

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

Development
-----------

You need to have *php* or *docker* installed to develop the library. To list all available commands run:

```bash
./run
```
