---
layout: default
title: Table of Contents Extension
description: The Table of Contents extension automatically inserts links to the headings in your document.
redirect_from: /extensions/table-of-contents/
---

# Table of Contents Extension

The `TableOfContentsExtension` automatically inserts a table of contents into your document with links to the various headings.

The [Heading Permalink](/2.7/extensions/heading-permalinks/) extension must also be included for this to work.

## Installation

This extension is bundled with `league/commonmark`. This library can be installed via Composer:

```bash
composer require league/commonmark
```

See the [installation](/2.7/installation/) section for more details.

## Usage

Configure your `Environment` as usual and simply add the `TableOfContentsExtension` and `HeadingPermalinkExtension` provided by this package:

```php
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\TableOfContents\TableOfContentsExtension;
use League\CommonMark\MarkdownConverter;

// Define your configuration, if needed
// Extension defaults are shown below
// If you're happy with the defaults, feel free to remove them from this array
$config = [
    'table_of_contents' => [
        'html_class' => 'table-of-contents',
        'position' => 'top',
        'style' => 'bullet',
        'min_heading_level' => 1,
        'max_heading_level' => 6,
        'normalize' => 'relative',
        'placeholder' => null,
    ],
];

// Configure the Environment with all the CommonMark parsers/renderers
$environment = new Environment($config);
$environment->addExtension(new CommonMarkCoreExtension());

// Add the two extensions
$environment->addExtension(new HeadingPermalinkExtension());
$environment->addExtension(new TableOfContentsExtension());

// Instantiate the converter engine and start converting some Markdown!
$converter = new MarkdownConverter($environment);
echo $converter->convert('# Awesome!');
```

## Configuration

This extension can be configured by providing a `table_of_contents` array with several nested configuration options.  The defaults are shown in the code example above.

### `html_class`

The value of this nested configuration option should be a `string` that you want set as the `<ul>` or `<ol>` tag's `class` attribute.  This defaults to `'table-of-contents'`.

### `normalize`

This should be a `string` that defines one of three different strategies to use when generating a (potentially-nested) list from your various headings:

- `'flat'`
- `'as-is'`
- **`'relative'`** (default)

See "[Normalization Strategies](#normalization-strategies)" below for more information.

### `position`

This `string` controls where in the document your table of contents will be placed.  There are two options:

- **`'top'`** (default) - Insert at the very top of the document, before any content
- `'before-headings'` - Insert just before the very first heading - useful if you want to have some descriptive text show above the table of content.
- `'placeholder'` - Location is manually defined by a user-provided placeholder somewhere in the document (see the `placeholder` option below)

If you'd like to customize this further, you can implement a [custom event listener](/2.7/customization/event-dispatcher/#registering-listeners) to locate the `TableOfContents` node and reposition it somewhere else in the document prior to rendering.

### `placeholder`

When combined with `'position' => 'placeholder'`, this setting tells the extension which placeholder content should be replaced with the Table of Contents.  For example, if you set this option to `[TOC]`, then any lines in your document consisting of that `[TOC]` placeholder will be replaced by the Table of Contents. Note that this option has no default value - you must provide this string yourself.

### `style`

This `string` option controls what style of HTML list should be used to render the table of contents:

- **`'bullet'`** (default) - Unordered, bulleted list (`<ul>`)
- `'ordered'` - Ordered list (`<ol>`)

### `min_heading_level` and `max_heading_level`

These two settings control which headings should appear in the list.  By default, all 6 levels (`1`, `2`, `3`, `4`, `5`, and `6`).  You can override this by setting the `min_heading_level` and/or `max_heading_level` to a different number (`int` value).

## Normalization Strategies

Consider this sample Markdown input:

```markdown
## Level 2 Heading

This is a sample document that starts with a level 2 heading

#### Level 4 Heading

Notice how we went from a level 2 heading to a level 4 heading!

### Level 3 Heading

And now we have a level 3 heading here.
```

Here's how the different normalization strategies would handle this input:

### Strategy: `'flat'`

All links in your table of contents will be shown in a flat, single-level list:

```html
<ul class="table-of-contents">
    <li>
        <p><a href="#level-2-heading">Level 2 Heading</a></p>
    </li>
    <li>
        <p><a href="#level-4-heading">Level 4 Heading</a></p>
    </li>
    <li>
        <p><a href="#level-3-heading">Level 3 Heading</a></p>
    </li>
</ul>

<!-- The rest of the content would go here -->
```

### Strategy: `'as-is'`

Level 1 headings (`<h1>`) will appear on the first level of the list, with level 2 headings (`<h2>`) nested under those, and so forth - exactly as they occur within the document.  But this can get weird if your document doesn't start with level 1 headings, or it doesn't properly nest the levels:

```html
<ul class="table-of-contents">
    <li>
        <ul>
            <li>
                <p><a href="#level-2-heading">Level 2 Heading</a></p>
                <ul>
                    <li>
                        <ul>
                            <li>
                                <p><a href="#level-4-heading">Level 4 Heading</a></p>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <p><a href="#level-3-heading">Level 3 Heading</a></p>
                    </li>
                </ul>
            </li>
        </ul>
    </li>
</ul>

<!-- The rest of the content would go here -->
```

### Strategy: `'relative'`

Applies nesting, but handles edge cases (like incorrect nesting levels) as you'd expect:

```html
<ul class="table-of-contents">
    <li>
        <p><a href="#level-2-heading">Level 2 Heading</a></p>
        <ul>
            <li>
                <p><a href="#level-4-heading">Level 4 Heading</a></p>
            </li>
        </ul>
        <ul>
            <li>
                <p><a href="#level-3-heading">Level 3 Heading</a></p>
            </li>
        </ul>
    </li>
</ul>

<!-- The rest of the content would go here -->
```
