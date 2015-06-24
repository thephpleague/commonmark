CommonMark Table Extension
==========================

[![Packagist](https://img.shields.io/packagist/v/webuni/commonmark-table-extension.svg?style=flat-square)](https://packagist.org/packages/webuni/commonmark-table-extension)
[![Build Status](https://img.shields.io/travis/webuni/commonmark-table-extension.svg?style=flat-square)](https://travis-ci.org/webuni/commonmark-table-extension)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/webuni/commonmark-table-extension.svg?style=flat-square)](https://scrutinizer-ci.com/g/webuni/commonmark-table-extension)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/d7a0bce6-6a3a-4b3c-abb1-0b69ad10513b.svg?style=flat-square)](https://insight.sensiolabs.com/projects/d7a0bce6-6a3a-4b3c-abb1-0b69ad10513b)

The Table extension adds the ability to create tables in CommonMark documents.

Installation
------------

This project can be installed via Composer:

    composer require webuni/commonmark-table-extension

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

```markdown
th | th(center) | th(right)
---|:----------:|----------:
td |     td     |         td
```

```markdown
| header 1 | header 2 | header 2 |
| :------- | :------: | -------: |
| cell 1.1 | cell 1.2 | cell 1.3 |
| cell 2.1 | cell 2.2 | cell 2.3 |
```

