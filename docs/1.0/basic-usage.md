---
layout: default
title: Basic Usage
redirect_from: /0.20/basic-usage/
---

Basic Usage
==============

The `CommonMarkConverter` class provides a simple wrapper for converting CommonMark to HTML:

~~~php
<?php

use League\CommonMark\CommonMarkConverter;

$converter = new CommonMarkConverter();
echo $converter->convertToHtml('# Hello World!');

// <h1>Hello World!</h1>
~~~

<i class="fa fa-exclamation-triangle"></i>
**Important:** See the [security](/1.0/security/) section for important details on avoiding security misconfigurations.

The actual conversion process requires three steps:

 1. Create an `Environment`, adding whichever extensions/parser/renders you need
 2. Parsing the Markdown input into an AST
 3. Rendering the AST document as HTML

You can do this yourself if you wish:

~~~php
<?php

use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;

$environment = Environment::createCommonMarkEnvironment();
$parser = new DocParser($environment);
$htmlRenderer = new HtmlRenderer($environment);

$markdown = '# Hello World!';

$document = $parser->parse($markdown);
echo $htmlRenderer->renderBlock($document);

// <h1>Hello World!</h1>
~~~

[Additional customization](/1.0/customization/overview/) is also possible.

Please note that only UTF-8 and ASCII encodings are supported.  If your Markdown uses a different encoding please convert it to UTF-8 before running it through this library.
