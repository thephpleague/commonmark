---
layout: default
title: Footnote Extension
description: The FootnoteExtension adds the ability to create footnotes in Markdown documents.
---

# Footnotes

The `FootnoteExtension` adds the ability to create footnotes in Markdown documents.

## Installation

This extension is bundled with `league/commonmark`. This library can be installed via Composer:

```bash
composer require league/commonmark
```

See the [installation](/2.5/installation/) section for more details.

## Footnote Syntax

Sample Markdown input:

```markdown
Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit.
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi[^note1] leo risus, porta ac consectetur ac.

[^note1]: Elit Malesuada Ridiculus
```

Result:

```html
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
                Elit Malesuada Ridiculus <a class="footnote-backref" rev="footnote" href="#fnref:note1">↩</a>
            </p>
        </li>
    </ol>
</div>
```

## Usage

Configure your `Environment` as usual and simply add the `FootnoteExtension`:

```php
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Footnote\FootnoteExtension;
use League\CommonMark\MarkdownConverter;

// Define your configuration, if needed
// Extension defaults are shown below
// If you're happy with the defaults, feel free to remove them from this array
$config = [
    'footnote' => [
        'backref_class'      => 'footnote-backref',
        'backref_symbol'     => '↩',
        'container_add_hr'   => true,
        'container_class'    => 'footnotes',
        'ref_class'          => 'footnote-ref',
        'ref_id_prefix'      => 'fnref:',
        'footnote_class'     => 'footnote',
        'footnote_id_prefix' => 'fn:',
    ],
];

// Configure the Environment with all the CommonMark parsers/renderers
$environment = new Environment($config);
$environment->addExtension(new CommonMarkCoreExtension());

// Add the extension
$environment->addExtension(new FootnoteExtension());

// Instantiate the converter engine and start converting some Markdown!
$converter = new MarkdownConverter($environment);
echo $converter->convert("This is a footnote[^test] test.\n\n[^test]: Doesn't it look good!");
```

## Configuration

This extension can be configured by providing a `footnote` array with several nested configuration options.  The defaults are shown in the code example above.

### `backref_class`

This `string` option defines which HTML class should be assigned to rendered footnote backreference elements.

### `backref_symbol`

This `string` option sets the symbol used as the contents of the footnote backreference link. It defaults to `\League\CommonMark\Extension\Footnote\Renderer\FootnoteBackrefRenderer::DEFAULT_SYMBOL = '↩'`.

If you want to use a custom icon, set this to an empty string `''` and take a look at the [Adding Icons](#adding-icons) section below.

> Note: Special HTML characters (`" & < >`) provided here will be escaped for security reasons.

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

## Adding Icons

You can use CSS to add a custom icon instead of providing a `backref_symbol`:

```php
$config = [
    'footnote' => [
        'backref_class' => 'footnote-backref',
        'backref_symbol' => '',
    ],
];
```

Then target the `backref_class` given in the configuration in your CSS:

```css
/**
 * Custom SVG Icon.
 */
.footnote-backref::after {
  display: inline-block;
  content: "";
  /**
   * Octicon Link (https://iconify.design/icon-sets/octicon/link.html)
   * [Pro Tip] Use an SVG URL encoder (https://yoksel.github.io/url-encoder).
   */
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' aria-hidden='true' style='-ms-transform:rotate(360deg);-webkit-transform:rotate(360deg)' viewBox='0 0 16 16' transform='rotate(360)'%3E%3Cpath fill-rule='evenodd' d='M4 9h1v1H4c-1.5 0-3-1.69-3-3.5S2.55 3 4 3h4c1.45 0 3 1.69 3 3.5 0 1.41-.91 2.72-2 3.25V8.59c.58-.45 1-1.27 1-2.09C10 5.22 8.98 4 8 4H4c-.98 0-2 1.22-2 2.5S3 9 4 9zm9-3h-1v1h1c1 0 2 1.22 2 2.5S13.98 12 13 12H9c-.98 0-2-1.22-2-2.5 0-.83.42-1.64 1-2.09V6.25c-1.09.53-2 1.84-2 3.25C6 11.31 7.55 13 9 13h4c1.45 0 3-1.69 3-3.5S14.5 6 13 6z' fill='%23626262'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-size: 1em;
}
```
