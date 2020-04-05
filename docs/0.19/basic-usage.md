---
layout: default
title: Basic Usage
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
**Important:** See the [security](/0.19/security/) section for important details on avoiding security misconfigurations.

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

[Additional customization](/0.19/customization/overview/) is also possible.
