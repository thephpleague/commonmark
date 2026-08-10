---
layout: default
title: Default Attributes Extension
description: The DefaultAttributesExtension allows you to apply default HTML classes and other attributes using configuration options.
redirect_from:
  - /extensions/default-attributes/
  - /2.0/extensions/default-attributes/
  - /2.1/extensions/default-attributes/
  - /2.2/extensions/default-attributes/
  - /2.3/extensions/default-attributes/
  - /2.4/extensions/default-attributes/
  - /2.5/extensions/default-attributes/
  - /2.6/extensions/default-attributes/
  - /2.7/extensions/default-attributes/
---

# Default Attributes

The `DefaultAttributesExtension` allows you to apply default HTML classes and other attributes using configuration options.

It works by applying the attributes to the nodes during the [`DocumentParsedEvent` event](/2.x/customization/abstract-syntax-tree/#documentparsedevent) - right after the nodes are parsed but before they are rendered.
(As a result, it's possible that renderers may add other attributes - the goal of this extension is only to provide custom defaults.)

## Installation

This extension is bundled with `league/commonmark`. This library can be installed via Composer:

```bash
composer require league/commonmark
```

See the [installation](/2.x/installation/) section for more details.

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
        'attributes' => [
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

The configuration above uses the format introduced in 2.10.  If you already have `default_attributes` configured from an earlier version, see [upgrading from the older format](#upgrading-from-the-older-format).

## Configuration

Provide a `default_attributes` array with an `attributes` key (available since 2.10).  Each key inside it should be a FQCN for the node class you wish to apply the default attribute to, and the values should be a map of attribute names to attribute values:

```php
$config = [
    'default_attributes' => [
        'attributes' => [
            Table::class => [
                'class' => 'table',
            ],
        ],
    ],
];
```

Attribute values may be any of the following types:

- `string`
- `string[]`
- `bool`
- `Closure` or invokable object (parameter is the `Node`, return value may be `string|string[]|bool`, or `null` to make no changes)

### Strings that are also PHP function names

PHP considers a string to be `callable` whenever it happens to match the name of a defined function, and there are quite a few short function names which also make perfectly reasonable CSS classes - `link`, `header`, `key`, `range`, and `current` among them.

Only a `Closure` or an invokable object is therefore treated as a callback.  Strings and arrays are always used as literal attribute values, so `'class' => 'link'` produces `class="link"` instead of calling PHP's [`link()`](https://www.php.net/manual/en/function.link.php) function.

If you have a callback written as a string or an array, wrap it with `Closure::fromCallable()`:

```php
$config = [
    'default_attributes' => [
        'attributes' => [
            Heading::class => [
                'class' => Closure::fromCallable([MyThemeHelper::class, 'headingClass']),
            ],
        ],
    ],
];
```

This is controlled by a `strict_callables` option, which is enabled by default in the format shown above.  You only need to set it yourself when using the older format below.

### Upgrading from the older format

This section only matters if you already have `default_attributes` configured - there's nothing here you need for new configuration.

Before 2.10, `default_attributes` was the node map on its own, without the surrounding `attributes` key.  That format still works throughout the rest of 2.x:

```php
$config = [
    'default_attributes' => [
        Table::class => [
            'class' => 'table',
        ],
    ],
];
```

The two formats behave identically except for the default value of `strict_callables`:

| Format | `strict_callables` defaults to |
| ------ | ------------------------------ |
| Structure with an `attributes` key (2.10 and later) | `true` |
| Node map on its own (any version) | `false` |

When it is disabled, any string or array which names a callable is invoked with the `Node` and its return value used as the attribute value - which is exactly what makes `'class' => 'link'` fail.  Configuration written before the option existed keeps that behavior so it continues working as it always has, and plain `callable` values remain allowed alongside the types listed above.

You can opt in without restructuring anything else:

```php
$config = [
    'default_attributes' => [
        Table::class => [
            'class' => 'table',
        ],
        'strict_callables' => true,
    ],
];
```

> Note: `strict_callables` will be removed in 3.0, when only closures and invokable objects will ever be treated as callbacks.  Enabling it now means no changes will be needed then.

## Examples

Here's an example that will apply Bootstrap 4 classes and attributes:

```php
$config = [
    'default_attributes' => [
        'attributes' => [
            Table::class => [
                'class' => ['table', 'table-responsive'],
            ],
            BlockQuote::class => [
                'class' => 'blockquote',
            ],
        ],
    ],
];
```

Here's a more complex example that uses a `Closure` to add a class only if the paragraph immediately follows an `<h1>` heading:

```php
$config = [
    'default_attributes' => [
        'attributes' => [
            Paragraph::class => [
                'class' => static function (Paragraph $paragraph) {
                    if ($paragraph->previous() instanceof Heading && $paragraph->previous()->getLevel() === 1) {
                        return 'lead';
                    }

                    return null;
                },
            ],
        ],
    ],
];
```
