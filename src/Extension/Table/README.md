CommonMark Table Extension
==========================

The Table extension adds the ability to create tables in CommonMark documents.

Usage
-----

Configure your `Environment` as usual and simply add the `TableExtension` provided by this package:

```php
use League\CommonMark\Converter;
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;
use League\CommonMark\Extension\Table\TableExtension;

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

Credits
-------

- [Martin Hasoň](https://github.com/hason)
- [Webuni s.r.o.](https://www.webuni.cz)
- [Colin O'Dell](https://github.com/colinodell)
