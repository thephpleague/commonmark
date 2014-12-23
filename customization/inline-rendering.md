---
layout: default
title: Inline Rendering
permalink: /customization/inline-rendering/
---

Inline Rendering
===============

Inline renderers are responsible for converting the parsed inline elements into their HTML representation.

All inline renderers should implement `InlineRendererInterface` and its `render()` method:

## render()

Block elements are responsible for calling `$htmlRenderer->renderInlines()` if they contain inline elements.  This in turns causes the `HtmlRenderer` to call this `render()` method whenever a supported inline element is encountered.

If the method can only handle certain inline types, be sure to verify that you've been passed the correct type.

### Parameters

* `AbstractBaseInline $inline` - The encountered inline you must render
* `HtmlRenderer $htmlRenderer` - The AST renderer; use this to help escape output or easily generate HTML tags

### Return value

The method must return the final, raw HTML represenation of the entire inline and any contents.  You are responsible for handling any
escaping that may be necessary.

## Designating Inline Renderers

When registering your render, you must tell the `Environment` which inline element class your renderer should handle. For example:

```php
$environment = Environment::createCommonMarkEnvironment();

// First param - the inline class type that should use our renderer
// Second param - instance of the block renderer
$environment->addBlockRenderer('Link', new MyCustomLinkRenderer());
```

## Example

Here's a custom renderer which puts a special class on links to external sites:

```php
class MyCustomLinkRenderer implements BlockRendererInterface
{
    private $host;

    public function __construct($host)
    {
        $this->host = $host;
    }

    public function render(AbstractInlineElement $inline, HtmlRenderer $htmlRenderer)
    {
        if (!($inline instanceof Link)) {
            throw new \InvalidArgumentException('Incompatible inline type: ' . get_class($inline));
        }

        $attrs = array();

        $attrs['href'] = $htmlRenderer->escape($inline->getUrl(), true);

        if (isset($inline->attributes['title'])) {
            $attrs['title'] = $htmlRenderer->escape($inline->attributes['title'], true);
        }

        if ($this->isExternalUrl($inline->getUrl())) {
            $attr['class'] = 'external-link';
        }

        return $htmlRenderer->inTags('a', $attrs, $htmlRenderer->renderInlines($inline->getLabel()->getInlines()));
    }

    private function isExternalUrl($url)
    {
        return parse_url($url, PHP_URL_HOST) !== $this->host;
    }
}

$environment = Environment::createCommonMarkEnvironment();
$environment->addInlineRenderer('Link', new MyCustomLinkRenderer());
```

## Tips

* Take advantage of `$htmlRenderer->inTags()` to simplify HTML tag creation
* Some inlines can contain other inlines - don't forget to render those too!

