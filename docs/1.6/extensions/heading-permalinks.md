---
layout: default
title: Heading Permalink Extension
description: The HeadingPermalinkExtension makes all header elements linkable
---

# Heading Permalink Extension

This extension makes all of your heading elements (`<h1>`, `<h2>`, etc) linkable so that users can quickly grab a link to that specific part of the document - almost like the headings in this documentation!

**Tip:** You can combine this with the [Table of Contents extension](/1.6/extensions/table-of-contents/) to automatically generate a list of links to the headings in your documents.

## Installation

This extension is bundled with `league/commonmark`. This library can be installed via Composer:

```bash
composer require league/commonmark
```

See the [installation](/1.6/installation/) section for more details.

## Usage

This extension can be added to any new `Environment`:

```php
use League\CommonMark\Environment;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkRenderer;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Normalizer\SlugNormalizer;

// Obtain a pre-configured Environment with all the CommonMark parsers/renderers ready-to-go
$environment = Environment::createCommonMarkEnvironment();

// Add this extension
$environment->addExtension(new HeadingPermalinkExtension());

// Set your configuration
$environment->mergeConfig([
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
]);

// Instantiate the converter engine and start converting some Markdown!
$converter = new MarkdownConverter($environment);
echo $converter->convertToHtml('# Hello World!');
```

## Configuration

This extension can be configured by providing a `heading_permalink` array with several nested configuration options.  The defaults are shown in the code example above.

### `html_class`

The value of this nested configuration option should be a `string` that you want set as the `<a>` tag's `class` attribute.  This defaults to `'heading-permalink'`.

### `id_prefix`

This should be a `string` you want prepended to HTML IDs.  This prevents generating HTML ID attributes which might conflict with others in your stylesheet.  A dash separator (`-`) will be added between the prefix and the ID.  You can instead set this to an empty string (`''`) if you don't want a prefix.

### `inner_contents` _(deprecated since `1.5.0`)_

This controls the HTML you want to appear inside of the generated `<a>` tag. Usually this would be something you would
style as some kind of link icon, but you can replace this with any custom HTML you wish.

This option was deprecated in 1.5.0 and will be removed in 2.0.0. Use the `symbol` option instead.

This option has no default value and if one is provided, a deprecation warning will be triggered and the `symbol`
config option below will be ignored completely.

### `insert`

This controls whether the anchor is added to the beginning of the `<h1>`, `<h2>` etc. tag or to the end.  Can be set to either `'before'` or `'after'`.

### `symbol`

This option sets the symbol used to display the permalink on the document. This defaults to `\League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkRenderer::DEFAULT_SYMBOL = '¶'`.

If you want to use a custom icon, then set this to an empty string `''` and check out the [Adding Icons](#adding-icons) sections below.

> Note: Special HTML characters (`" & < >`) provided here will be escaped for security reasons.

### `title`

This option sets the `title` attribute on the `<a>` tag.  This defaults to `'Permalink'`.

### `slug_normalizer`

"Slugs" are the strings used within the `href`, `name`, and `id` attributes to identify a particular permalink.
By default, this extension will generate slugs based on the contents of the heading, just like GitHub-Flavored Markdown does.

You can change the string that is used as the "slug" by setting the `slug_normalizer` option to any class that implements `TextNormalizerInterface`.

For example, if you'd like each slug to be an MD5 hash, you could create a class like this:

```php
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

Then targeting the `html_class` given in the configuration in your CSS:

```css
/**
 * Custom SVG Icon.
 */
.heading-permalink::after {
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
