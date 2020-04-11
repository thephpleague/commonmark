---
layout: homepage
---

{% assign version = site.data.project.default_version %}

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

## Included Extensions

This project includes [several built-in extensions you can use](/{{ version }}/extensions/) to enable additional features and syntax.

## Customization

This library allows you to add custom syntax, renderers, and more.  Check out the [Customization](/{{ version }}/customization/overview/) section for more information.

## Community Integrations & Extensions

An updated list of pre-built integrations and extensions can be found in the [Related Packages](https://github.com/thephpleague/commonmark#%EF%B8%8F-related-packages) section of the `README`.

# Sponsors

We'd like to thank the following people for supporting the ongoing development of this project:

 - [RIPS Technologies](https://www.ripstech.com/) for supporting this project with a complimentary [RIPS SaaS](https://www.ripstech.com/product/) license
 - [JetBrains](https://www.jetbrains.com/) for supporting this project with complimentary [PhpStorm](https://www.jetbrains.com/phpstorm/) licenses

Are you interested in sponsoring this project? [Make a pledge via Patreon](https://www.patreon.com/join/colinodell) and we may include your name here!
