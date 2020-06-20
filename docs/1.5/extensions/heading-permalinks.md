---
layout: default
title: Heading Permalink Extension
description: The HeadingPermalinkExtension makes all header elements linkable
redirect_from: /extensions/heading-permalinks/
---

# Heading Permalink Extension

This extension makes all of your heading elements (`<h1>`, `<h2>`, etc) linkable so that users can quickly grab a link to that specific part of the document - almost like the headings in this documentation!

**Tip:** You can combine this with the [Table of Contents extension](/1.5/extensions/table-of-contents/) to automatically generate a list of links to the headings in your documents.

## Usage

This extension can be added to any new `Environment`:

```php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkRenderer;
use League\CommonMark\Normalizer\SlugNormalizer;

// Obtain a pre-configured Environment with all the CommonMark parsers/renderers ready-to-go
$environment = Environment::createCommonMarkEnvironment();

// Add this extension
$environment->addExtension(new HeadingPermalinkExtension());

// Set your configuration
$config = [
    // Extension defaults are shown below
    // If you're happy with the defaults, feel free to remove them from this array
    'heading_permalink' => [
        'html_class' => 'heading-permalink',
        'id_prefix' => 'user-content',
        'insert' => 'before',
        'title' => 'Permalink',
        'symbol' => HeadingPermalinkRenderer::DEFAULT_SYMBOL,
        'slug_normalizer' => new SlugNormalizer(),
    ],
];

// Instantiate the converter engine and start converting some Markdown!
$converter = new CommonMarkConverter($config, $environment);
echo $converter->convertToHtml('# Hello World!');
```

## Configuration

This extension can be configured by providing a `heading_permalink` array with several nested configuration options.  The defaults are shown in the code example above.

### `html_class`

The value of this nested configuration option should be a `string` that you want set as the `<a>` tag's `class` attribute.  This defaults to `'heading-permalink'`.

### `id_prefix`

This should be a `string` you want prepended to HTML IDs.  This prevents generating HTML ID attributes which might conflict with others in your stylesheet.  A dash separator (`-`) will be added between the prefix and the ID.  You can instead set this to an empty string (`''`) if you don't want a prefix.

### `inner_contents` _(deprecated since `1.5.0`)_

This controls the HTML you want to appear inside of the generated `<a>` tag.  Usually this would be something you'd style as some kind of link icon, but you can replace this with any custom HTML you wish.

From versions `1.4.0` to `1.4.3`, the default value for this config option was an embedded [Octicon link SVG](https://iconify.design/icon-sets/octicon/link.html).

In order to deprecate this config option, its default value had to be removed in version `1.5.0`. While this is
technically a breaking change, it can be easily restored by setting this config option to the same constant as before
(note: this constant has also been deprecated):

```php
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkRenderer;

$config = [
    'heading_permalink' => [
        'inner_contents' => HeadingPermalinkRenderer::DEFAULT_INNER_CONTENTS,
    ],
];
```

Whenever this config option is provided a value, a deprecation warning will be triggered and the `symbol` config
option below will be ignored completely.

### `insert`

This controls whether the anchor is added to the beginning of the `<h1>`, `<h2>` etc. tag or to the end.  Can be set to either `'before'` or `'after'`.

### `symbol`

This option sets the symbol used to display the permalink on the document. This defaults to `\League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkRenderer::DEFAULT_SYMBOL = '¶'`.

If you want to use a custom icon, then set this to an empty string `''` and check out the [Adding Icons](#adding-icons) sections below.

> Note: the symbol should only be is a single character value; additional characters will be stripped (does not affect multibyte characters).

### `title`

This option sets the `title` attribute on the `<a>` tag.  This defaults to `'Permalink'`.

### `slug_normalizer`

"Slugs" are the strings used within the `href`, `name`, and `id` attributes to identify a particular permalink.
By default, this extension will generate slugs based on the contents of the heading, just like Github-Flavored Markdown does.

You can change the string that is used as the "slug" by setting the `slug_normalizer` option to any class that implements `TextNormalizerInterface`.

For example, if you'd like each slug to be an MD5 hash, you could create a class like this:

```php
<?php

use League\CommonMark\Normalizer\TextNormalizerInterface;

final class MD5Normalizer implements TextNormalizerInterface
{
    public function normalize(string $text, $context = null): string
    {
        return md5($text);
    }
}
```

And then configure it like this:

```php
$config = [
    'heading_permalink' => [
        // ... other options here ...
        'slug_normalizer' => new MD5Normalizer(),
    ],
];
```

Or you could use [PHP's anonymous class feature](https://www.php.net/manual/en/language.oop5.anonymous.php) to define the generator's behavior without creating a new class file:

```php
$config = [
    'heading_permalink' => [
        // ... other options here ...
        'slug_normalizer' => new class implements TextNormalizerInterface {
            public function normalize(string $text, $context = null): string
            {
                // TODO: Implement your code here
            }
        },
    ],
];
```

## Example

If you wanted to style your headings exactly like this documentation page does, try this configuration!

```php
$config = [
    'heading_permalink' => [
        'html_class' => 'heading-permalink',
        'insert' => 'after',
        'symbol' => '¶',
        'title' => "Permalink",
    ],
];
```

Along with this CSS:

```css
.heading-permalink {
    font-size: .8em;
    vertical-align: super;
    text-decoration: none;
    color: transparent;
}

h1:hover .heading-permalink,
h2:hover .heading-permalink,
h3:hover .heading-permalink,
h4:hover .heading-permalink,
h5:hover .heading-permalink,
h6:hover .heading-permalink,
.heading-permalink:hover {
    text-decoration: none;
    color: #777;
}
```

## Styling Ideas

This library doesn't provide any CSS styling for the anchor element(s), but here are some ideas you could use in your own stylesheet.

You could hide the icon until the user hovers over the heading:

```css
.heading-permalink {
  visibility: hidden;
}

h1:hover .heading-permalink,
h2:hover .heading-permalink,
h3:hover .heading-permalink,
h4:hover .heading-permalink,
h5:hover .heading-permalink,
h6:hover .heading-permalink
{
  visibility: visible;
}
```

You could also float the symbol just a little bit left of the heading:

```css
.heading-permalink {
  float: left;
  padding-right: 4px;
  margin-left: -20px;
  line-height: 1;
}
```

These are only ideas - feel free to customize this however you'd like!

## Adding Icons

You can also use CSS to add a custom icon instead of providing a `symbol`:

```php
$config = [
    'heading_permalink' => [
        'html_class' => 'heading-permalink',
        'symbol' => '',
    ],
];
```

Then targeting the `html_class` given in the configuration in your CSS (example in SCSS):

```scss
// Font Awesome (https://fontawesome.com/icons/link).
.heading-permalink::after {
   @extend .fa;       // Extend from font-awesome base styles.
   content: "\f0c1";  // fa-link icon unicode.
}

// Bootstrap 3 Glyphicon (https://getbootstrap.com/docs/3.3/components/).
.heading-permalink::after {
  @extend .glyphicon; // Extend from Glyphicon base styles.
  content: "\e144";   // glyphicon-link icon unicode.
}

// Custom SVG/Bootstrap Icons.
.heading-permalink::after {
  display: inline-block;
  content: "";
  // Tip: use an SVG URL encoder (https://yoksel.github.io/url-encoder).
  // https://icons.getbootstrap.com/icons/box-arrow-up-right/
  background-image: url("data:image/svg+xml,%3Csvg class='bi bi-box-arrow-up-right' viewBox='0 0 16 16' fill='currentColor' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath fill-rule='evenodd' d='M1.5 13A1.5 1.5 0 003 14.5h8a1.5 1.5 0 001.5-1.5V9a.5.5 0 00-1 0v4a.5.5 0 01-.5.5H3a.5.5 0 01-.5-.5V5a.5.5 0 01.5-.5h4a.5.5 0 000-1H3A1.5 1.5 0 001.5 5v8zm7-11a.5.5 0 01.5-.5h5a.5.5 0 01.5.5v5a.5.5 0 01-1 0V2.5H9a.5.5 0 01-.5-.5z'/%3E%3Cpath fill-rule='evenodd' d='M14.354 1.646a.5.5 0 010 .708l-8 8a.5.5 0 01-.708-.708l8-8a.5.5 0 01.708 0z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-size: 1em 1em;
}
```
