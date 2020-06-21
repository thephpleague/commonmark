---
layout: default
title: Footnote Extension
description: The FootnoteExtension adds the ability to create footnotes in Markdown documents.
redirect_from: /extensions/footnotes/
---

# Footnotes

The `FootnoteExtension` adds the ability to create footnotes in Markdown documents.

## Footnote Syntax

Sample Markdown input:

```md
Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit.
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi[^note1] leo risus, porta ac consectetur ac.

[^note1]: Elit Malesuada Ridiculus
```

Result:

```md
<p>
    Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit.
    Lorem ipsum dolor sit amet, consectetur adipiscing elit.
    Morbi<sup id="fnref:note1"><a class="footnote-ref" href="#fn:note1" role="doc-noteref">1</a></sup> leo risus, porta ac consectetur ac.
</p>
<div class="footnotes">
    <hr />
    <ol>
        <li class="footnote" id="fn:note1">
            <p>
                Elit Malesuada Ridiculus <a class="footnote-backref" rev="footnote" href="#fnref:note1">&#8617;</a>
            </p>
        </li>
    </ol>
</div>
```

## Usage

Configure your `Environment` as usual and simply add the `FootnoteExtension`:

```php
<?php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\Footnote\FootnoteExtension;

// Obtain a pre-configured Environment with all the CommonMark parsers/renderers ready-to-go
$environment = Environment::createCommonMarkEnvironment();

// Add the extension
$environment->addExtension(new FootnoteExtension());

// Set your configuration
$config = [
    // Extension defaults are shown below
    // If you're happy with the defaults, feel free to remove them from this array
    'footnote' => [
        'backref_class'      => 'footnote-backref',
        'container_add_hr'   => true,
        'container_class'    => 'footnotes',
        'ref_class'          => 'footnote-ref',
        'ref_id_prefix'      => 'fnref:',
        'footnote_class'     => 'footnote',
        'footnote_id_prefix' => 'fn:',
    ],
];

// Instantiate the converter engine and start converting some Markdown!
$converter = new CommonMarkConverter($config, $environment);
echo $converter->convertToHtml('# Hello World!');
```

## Configuration

This extension can be configured by providing a `footnote` array with several nested configuration options.  The defaults are shown in the code example above.

### `backref_class`

This `string` option defines which HTML class should be assigned to rendered footnote backreference elements.

### `container_add_hr`

This `boolean` option controls whether an `<hr>` element should be added inside the container.  Set this to `false` if you want more control over how the footnote section at the bottom is differentiated from the rest of the document.

### `container_class`

This `string` option defines which HTML class should be assigned to the container at the bottom of the page which shows all the footnotes.

### `ref_class`

This `string` option defines which HTML class should be assigned to rendered footnote reference elements.

### `ref_id_prefix`

This `string` option defines the prefix prepended to footnote references.

### `footnote_class`

This `string` option defines which HTML class should be assigned to rendered footnote elements.

### `footnote_id_prefix`

This `string` option defines the prefix prepended to footnote elements.
