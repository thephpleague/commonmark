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
use League\CommonMark\Extension\HeadingPermalink\Slug\DefaultSlugGenerator;

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
        'inner_contents' => HeadingPermalinkRenderer::DEFAULT_INNER_CONTENTS,
        'insert' => 'before',
        'title' => 'Permalink',
        'slug_generator' => new DefaultSlugGenerator(),
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

### `inner_contents`

This controls the HTML you want to appear inside of the generated `<a>` tag.  Usually this would be something you'd style as some kind of link icon.

By default, we provide an embedded [Octicon link SVG](https://octicons.github.com/icon/link/), but you can replace this with any custom HTML you wish.

### `insert`

This controls whether the anchor is added to the beginning of the `<h1>`, `<h2>` etc. tag or to the end.  Can be set to either `'before'` or `'after'`.

### `title`

This option sets the `title` attribute on the `<a>` tag.  This defaults to `'Permalink'`.

### `slug_generator`

"Slugs" are the strings used within the `href`, `name`, and `id` attributes to identify a particular permalink.
By default, this extension will generate slugs based on the contents of the heading, just like Github-Flavored Markdown does.

You can change the string that is used as the "slug" by setting the `slug_generator` option to any class that implements `SlugGeneratorInterface`.

For example, if you'd like each slug to be an MD5 hash, you could create a class like this:

```php
<?php

use League\CommonMark\Extension\HeadingPermalink\Slug\SlugGeneratorInterface;

final class HashSlugGenerator implements SlugGeneratorInterface
{
    public function createSlug(string $input): string
    {
        return md5($input);
    }
}
```

And then configure it like this:

```php
$config = [
    'heading_permalink' => [
        // ... other options here ...
        'slug_generator' => new HashSlugGenerator(),
    ],
];
```

Or you could use [PHP's anonymous class feature](https://www.php.net/manual/en/language.oop5.anonymous.php) to define the generator's behavior without creating a new class file:

```php
$config = [
    'heading_permalink' => [
        // ... other options here ...
        'slug_generator' => new class implements SlugGeneratorInterface {
            public function createSlug(string $input): string
            {
                return md5($input);
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
        'inner_contents' => '¶',
        'insert' => 'after',
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

You could also float the icon just a little bit left of the heading:

```css
.heading-permalink {
  float: left;
  padding-right: 4px;
  margin-left: -20px;
  line-height: 1;
}
```

These are only ideas - feel free to customize this however you'd like!
