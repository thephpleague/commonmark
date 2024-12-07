---
layout: default
title: Default Attributes Extension
description: The DefaultAttributesExtension allows you to apply default HTML classes and other attributes using configuration options.
---

# Default Attributes

The `DefaultAttributesExtension` allows you to apply default HTML classes and other attributes using configuration options.

It works by applying the attributes to the nodes during the [`DocumentParsedEvent` event](/2.5/customization/abstract-syntax-tree/#documentparsedevent) - right after the nodes are parsed but before they are rendered.
(As a result, it's possible that renderers may add other attributes - the goal of this extension is only to provide custom defaults.)

## Installation

This extension is bundled with `league/commonmark`. This library can be installed via Composer:

```bash
composer require league/commonmark
```

See the [installation](/2.5/installation/) section for more details.

## Usage

Configure your `Environment` as usual and simply add the `DefaultAttributesExtension`:

```php
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\DefaultAttributes\DefaultAttributesExtension;
use League\CommonMark\Extension\Table\Table;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Node\Block\Paragraph;

// Define your configuration, if needed
// Extension defaults are shown below
// If you're happy with the defaults, feel free to remove them from this array
$config = [
    'default_attributes' => [
        Heading::class => [
            'class' => static function (Heading $node) {
                if ($node->getLevel() === 1) {
                    return 'title-main';
                } else {
                    return null;
                }
            },
        ],
        Table::class => [
            'class' => 'table',
        ],
        Paragraph::class => [
            'class' => ['text-center', 'font-comic-sans'],
        ],
        Link::class => [
            'class' => 'btn btn-link',
            'target' => '_blank',
        ],
    ],
];

// Configure the Environment with all the CommonMark parsers/renderers
$environment = new Environment($config);
$environment->addExtension(new CommonMarkCoreExtension());

// Add the extension
$environment->addExtension(new DefaultAttributesExtension());

// Instantiate the converter engine and start converting some Markdown!
$converter = new MarkdownConverter($environment);
echo $converter->convert('# Hello World!');
```

## Configuration

This extension can be configured by providing a `default_attributes` array.  Each key in the array should be a FQCN for the node class you wish to apply the default attribute to, and the values should be a map of attribute names to attribute values.

Attribute values may be any of the following types:

- `string`
- `string[]`
- `bool`
- `callable` (parameter is the `Node`, return value may be `string|string[]|bool`)

## Examples

Here's an example that will apply Bootstrap 4 classes and attributes:

```php
$config = [
    'default_attributes' => [
        Table::class => [
            'class' => ['table', 'table-responsive'],
        ],
        BlockQuote::class => [
            'class' => 'blockquote',
        ],
    ],
];
```

Here's a more complex example that uses a `callable` to add a class only if the paragraph immediately follows an `<h1>` heading:

```php
$config = [
    'default_attributes' => [
        Paragraph::class => [
            'class' => static function (Paragraph $paragraph) {
                if ($paragraph->previous() instanceof Heading && $paragraph->previous()->getLevel() === 1) {
                    return 'lead';
                }

                return null;
            },
        ],
    ],
];
```
