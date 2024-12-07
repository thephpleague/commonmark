---
layout: default
title: Table Extension
description: The TableExtension adds the ability to create tables in CommonMark documents
---

# Table Extension

_(Note: this extension is included by default within [the GFM extension](/2.4/extensions/github-flavored-markdown/))_

The `TableExtension` adds the ability to create tables in CommonMark documents.

## Installation

This extension is bundled with `league/commonmark`. This library can be installed via Composer:

```bash
composer require league/commonmark
```

See the [installation](/2.4/installation/) section for more details.

## Usage

Configure your `Environment` as usual and simply add the `TableExtension` provided by this package:

```php
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter;

// Define your configuration, if needed
$config = [
    'table' => [
        'wrap' => [
            'enabled' => false,
            'tag' => 'div',
            'attributes' => [],
        ],
        'alignment_attributes' => [
            'left'   => ['align' => 'left'],
            'center' => ['align' => 'center'],
            'right'  => ['align' => 'right'],
        ],
    ],
];

// Configure the Environment with all the CommonMark parsers/renderers
$environment = new Environment($config);
$environment->addExtension(new CommonMarkCoreExtension());

// Add this extension
$environment->addExtension(new TableExtension());

// Instantiate the converter engine and start converting some Markdown!
$converter = new MarkdownConverter($environment);
echo $converter->convert('Some Markdown with a table in it');
```

## Syntax

This package is fully compatible with [GFM-style tables](https://github.github.com/gfm/#tables-extension-):

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
<th align="left">th</th>
<th align="center">th(center)</th>
<th align="right">th(right)/th>
</tr>
</thead>
<tbody>
<tr>
<td align="left">td</td>
<td align="center">td</td>
<td align="right">td</td>
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

## Configuration

### Wrapping Container

You can "wrap" the table with a container element by configuring the following options:

- `enabled`: (`boolean`) Whether to wrap the table with a container element. Defaults to `false`.
- `tag`: (`string`) The tag name of the container element. Defaults to `div`.
- `attributes`: (`array`) An array of attributes to apply to the container element. Defaults to `[]`.

For example, to wrap all tables within a `<div class="table-responsive">` container element:

```php
$config = [
    'table' => [
        'wrap' => [
            'enabled' => true,
            'tag' => 'div',
            'attributes' => ['class' => 'table-responsive'],
        ],
    ],
];
```

### Alignment

You can configure the HTML attributes used for alignment via the `alignment_attributes` option. For example, if you wanted Bootstrap classes to be applied, you could do so like this:

```php
$config = [
    'table' => [
        'alignment_attributes' => [
            'left' => ['class' => 'text-start'],
            'center' => ['class' => 'text-center'],
            'right' => ['class' => 'text-end'],
        ],
    ],
];
```

Or you could use inline styles:

```php
$config = [
    'table' => [
        'alignment_attributes' => [
            'left' => ['style' => 'text-align:left'],
            'center' => ['style' => 'text-align:center'],
            'right' => ['style' => 'text-align:right'],
        ],
    ],
];
```

Or any other HTML attributes you'd like!

## Credits

The Table functionality was originally built by [Martin Hasoň](https://github.com/hason) and [Webuni s.r.o.](https://www.webuni.cz) before it was merged into the core parser.
