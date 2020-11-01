---
layout: default
title: External Links Extension
description: The ExternalLinksExtension detects external links and adjusts their HTML markup
---

# External Links Extension

This extension can detect links to external sites and adjust the markup accordingly:

- Adds a `rel="noopener noreferrer"` attribute
- Optionally adds any custom HTML classes

## Installation

This extension is bundled with `league/commonmark`. This library can be installed via Composer:

```bash
composer require league/commonmark
```

See the [installation](/1.3/installation/) section for more details.

## Usage

Configure your `Environment` as usual and simply add the `ExternalLinkExtension` provided by this package:

```php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;

// Obtain a pre-configured Environment with all the CommonMark parsers/renderers ready-to-go
$environment = Environment::createCommonMarkEnvironment();

// Add this extension
$environment->addExtension(new ExternalLinkExtension());

// Set your configuration
$config = [
    'external_link' => [
        'internal_hosts' => 'www.example.com', // Don't forget to set this!
        'open_in_new_window' => true,
        'html_class' => 'external-link',
    ],
];

// Instantiate the converter engine and start converting some Markdown!
$converter = new CommonMarkConverter($config, $environment);
echo $converter->convertToHtml('I successfully installed the <https://github.com/thephpleague/commonmark> project!');
```

## Configuration

This extension supports three configuration options under the `external_link` configuration:

### `internal_hosts`

This option defines a list of hosts which are considered non-external and should not receive the external link treatment.

This can be a single host name, like `'example.com'`, which must match **exactly**.

Wildcard matching is also supported using regular expression like `'/(^|\.)example\.com$/'`.  Note that you must use `/` characters to delimit your regex.

This configuration option also accepts an array of multiple strings and/or regexes:

```php
$config = [
    'external_link' => [
        'internal_hosts' => ['foo.example.com', 'bar.example.com', '/(^|\.)google\.com$/],
    ],
];
```

By default, if this option is not provided, all links will be considered external.

### `open_in_new_window`

This option (which defaults to `false`) determines whether any external links should open in a new tab/window.

### `html_class`

This option allows you to provide a `string` containing one or more HTML classes that should be added to the external link `<a>` tags:  No classes are added by default.

## Advanced Rendering

When an external link is detected, the `ExternalLinkProcessor` will set the `external` data option on the `Link` node to either `true` or `false`.  You can therefore create a [custom link renderer](/1.3/customization/inline-rendering/) which checks this value and behaves accordingly:

```php
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;
class MyCustomLinkRenderer implements InlineRendererInterface
{

    /**
     * @param Link                     $inline
     * @param ElementRendererInterface $htmlRenderer
     *
     * @return HtmlElement
     */
    public function render(AbstractInline $inline, ElementRendererInterface $htmlRenderer)
    {
        if (!($inline instanceof Link)) {
            throw new \InvalidArgumentException('Incompatible inline type: ' . \get_class($inline));
        }

        if ($inline->getData('external')) {
            // This is an external link - render it accordingly
        } else {
            // This is an internal link
        }

        // ...
    }
}
```

## Adding Icons

You can also use CSS to automagically add an external link icon by targeting the `html_class` given in the configuration:

```css
// Font Awesome example:
a[target="_blank"]::after,
a.external::after {
   content: "\f08e";
   font: normal normal normal 14px/1 FontAwesome;
}

// Glyphicon example:
a[target="_blank"]::after,
a.external::after {
  @extend .glyphicon;
  content: "\e164";
  margin-left: .5em;
  margin-right: .25em;
}
```
