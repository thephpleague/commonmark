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

See the [installation](/1.4/installation/) section for more details.

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

When an external link is detected, the `ExternalLinkProcessor` will set the `external` data option on the `Link` node to either `true` or `false`.  You can therefore create a [custom link renderer](/1.4/customization/inline-rendering/) which checks this value and behaves accordingly:

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

You can also use CSS to add a custom icon by targeting the `html_class` given in the configuration:

```php
$config = [
    'external_link' => [
        'html_class' => 'external',
    ],
];
```

```css
/**
 * Custom SVG Icon.
 */
a[target="_blank"]::after,
a.external::after {
  display: inline-block;
  content: "";
  /**
   * Octicon Link External (https://iconify.design/icon-sets/octicon/link-external.html)
   * [Pro Tip] Use an SVG URL encoder (https://yoksel.github.io/url-encoder).
   */
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' aria-hidden='true' style='-ms-transform:rotate(360deg);-webkit-transform:rotate(360deg)' viewBox='0 0 12 16' transform='rotate(360)'%3E%3Cpath fill-rule='evenodd' d='M11 10h1v3c0 .55-.45 1-1 1H1c-.55 0-1-.45-1-1V3c0-.55.45-1 1-1h3v1H1v10h10v-3zM6 2l2.25 2.25L5 7.5 6.5 9l3.25-3.25L12 8V2H6z' fill='%23626262'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-size: 1em;
}
```
