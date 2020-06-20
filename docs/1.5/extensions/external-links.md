---
layout: default
title: External Links Extension
description: The ExternalLinksExtension detects external links and adjusts their HTML markup
redirect_from: /extensions/external-links/
---

# External Links Extension

This extension can detect links to external sites and adjust the markup accordingly:

 - Make the links open in new tabs/windows
 - Adds a `rel` attribute to the resulting `<a>` tag with values like `"nofollow noopener noreferrer"`
 - Optionally adds any custom HTML classes

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
        'internal_hosts' => 'www.example.com', // TODO: Don't forget to set this!
        'open_in_new_window' => true,
        'html_class' => 'external-link',
        'nofollow' => '',
        'noopener' => 'external',
        'noreferrer' => 'external',
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

### `nofollow`, `noopener`, and `noreferrer`

These options allow you to configure whether a `rel` attribute should be applied to links.  Each of these options can be set to one of the following `string` values:

 - `'external'` - **Apply to external links only (default)**
 - `'internal'` - Apply to internal links only
 - `'all'` - Apply to all links (both internal and external)
 - `''` (empty string) - Don't apply to any links

## Advanced Rendering

When an external link is detected, the `ExternalLinkProcessor` will set the `external` data option on the `Link` node to either `true` or `false`.  You can therefore create a [custom link renderer](/1.5/customization/inline-rendering/) which checks this value and behaves accordingly:

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

```scss
// Font Awesome (https://fontawesome.com/icons/external-link-alt).
a[target="_blank"]::after,
a.external::after {
   @extend .fa;       // Extend from font-awesome base styles.
   content: "\f35d";  // fa-external-link-alt icon unicode.
}

// Bootstrap 3 Glyphicon (https://getbootstrap.com/docs/3.3/components/).
a[target="_blank"]::after,
a.external::after {
  @extend .glyphicon; // Extend from Glyphicon base styles.
  content: "\e164";   // glyphicon-new-window icon unicode.
}

// Custom SVG/Bootstrap Icons.
a[target="_blank"]::after,
a.external::after {
  display: inline-block;
  content: "";
  // Tip: use an SVG URL encoder (https://yoksel.github.io/url-encoder).
  // https://icons.getbootstrap.com/icons/link-45deg/
  background-image: url("data:image/svg+xml,%3Csvg class='bi bi-link-45deg' viewBox='0 0 16 16' fill='currentColor' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M4.715 6.542L3.343 7.914a3 3 0 104.243 4.243l1.828-1.829A3 3 0 008.586 5.5L8 6.086a1.001 1.001 0 00-.154.199 2 2 0 01.861 3.337L6.88 11.45a2 2 0 11-2.83-2.83l.793-.792a4.018 4.018 0 01-.128-1.287z'/%3E%3Cpath d='M5.712 6.96l.167-.167a1.99 1.99 0 01.896-.518 1.99 1.99 0 01.518-.896l.167-.167A3.004 3.004 0 006 5.499c-.22.46-.316.963-.288 1.46z'/%3E%3Cpath d='M6.586 4.672A3 3 0 007.414 9.5l.775-.776a2 2 0 01-.896-3.346L9.12 3.55a2 2 0 012.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 00-4.243-4.243L6.586 4.672z'/%3E%3Cpath d='M10 9.5a2.99 2.99 0 00.288-1.46l-.167.167a1.99 1.99 0 01-.896.518 1.99 1.99 0 01-.518.896l-.167.167A3.004 3.004 0 0010 9.501z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-size: 1em 1em;
}
```
