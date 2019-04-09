---
layout: homepage
---

{% assign version = site.data.project.releases.current.version %}

# Features

## Easy Usage

```php
use League\CommonMark\CommonMarkConverter;

$converter = new CommonMarkConverter();
echo $converter->convertToHtml('# Hello World!');

// <h1>Hello World!</h1>
```

## Security

All CommonMark features are supported by default, including raw HTML and unsafe links, which you may want to disable using the `html_input` and `allow_unsafe_links` options:

```php
use League\CommonMark\CommonMarkConverter;

$converter = new CommonMarkConverter(['html_input' => 'escape', 'allow_unsafe_links' => false]);
echo $converter->convertToHtml('# Hello World!');

// <h1>Hello World!</h1>
```

## Customization

This library allows you to add custom syntax, renderers, and more.  Check out the [Customization](/{{ version }}/customization/overview/) section for more information.

## Integrations & Community Extensions

An updated list of pre-built integrations and extensions can be found in the [Related Packages](https://github.com/thephpleague/commonmark#related-packages) section of the `README`.
