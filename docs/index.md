---
layout: homepage
---
<!-- markdownlint-disable first-line-heading -->
{% assign version = site.data.project.default_version %}
<!-- markdownlint-restore -->

# Features

## Easy Usage

```php
use League\CommonMark\CommonMarkConverter;

$converter = new CommonMarkConverter();
echo $converter->convert('# Hello World!');

// <h1>Hello World!</h1>
```

## Security

All CommonMark features are supported by default, including raw HTML and unsafe links, which you may want to disable using the `html_input` and `allow_unsafe_links` options:

```php
use League\CommonMark\CommonMarkConverter;

$converter = new CommonMarkConverter(['html_input' => 'escape', 'allow_unsafe_links' => false]);
echo $converter->convert('# Hello World!');

// <h1>Hello World!</h1>
```

## Included Extensions

This project includes [several built-in extensions you can use](/{{ version }}/extensions/overview/) to enable additional features and syntax.

## Customization

This library allows you to add custom syntax, renderers, and more.  Check out the [Customization](/{{ version }}/customization/overview/) section for more information.

## Community Integrations & Extensions

An updated list of pre-built integrations and extensions can be found in the [Related Packages](https://github.com/thephpleague/commonmark#%EF%B8%8F-related-packages) section of the `README`.
