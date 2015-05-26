CommonMark Table Extension
==========================

The Table extension adds the ability to create tables in CommonMark documents.

Installation
------------

This project can be installed via Composer:

    composer require webuni/commonmark-table-extension
    
Usage
-----

```php
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;
use Webuni\CommonMark\TableExtension\TableExtension;

$environment = Environment::createCommonMarkEnvironment();
$environment->addExtension(new TableExtension());

$parser = new DocParser($environment);
$renderer = new HtmlRenderer($environment);

// Here's our sample input
$markdown = '# Hello World!';

$documentAST = $parser->parse($markdown);
echo $renderer->renderBlock($documentAST);
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

