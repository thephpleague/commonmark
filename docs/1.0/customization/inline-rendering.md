---
layout: default
title: Inline Rendering
description: Customizing the output when rendering inline elements
redirect_from: /0.20/customization/inline-rendering/
---

# Inline Rendering

Inline renderers are responsible for converting the parsed inline elements into their HTML representation.

All inline renderers should implement `InlineRendererInterface` and its `render()` method:

## render()

Block elements are responsible for calling `$htmlRenderer->renderInlines()` if they contain inline elements.  This in turns causes the `HtmlRenderer` to call this `render()` method whenever a supported inline element is encountered.

If the method can only handle certain inline types, be sure to verify that you've been passed the correct type.

### Parameters

- `AbstractInline $inline` - The encountered inline you must render
- `ElementRendererInterface $htmlRenderer` - The AST renderer; use this to help escape output or easily generate HTML tags

### Return value

The method must return the final HTML representation of the entire inline and any contents. This can be an `HtmlElement` object (preferred; castable to a string) or a string of raw HTML.

You are responsible for handling any escaping that may be necessary.

Return `null` if your renderer cannot handle the given inline element - the next-highest priority renderer will then be given a chance to render it.

## Designating Inline Renderers

When registering your render, you must tell the `Environment` which inline element class your renderer should handle. For example:

```php
use League\CommonMark\Environment;

$environment = Environment::createCommonMarkEnvironment();

// First param - the inline class type that should use our renderer
// Second param - instance of the block renderer
$environment->addInlineRenderer('League\CommonMark\Inline\Element\Link', new MyCustomLinkRenderer());
```

## Example

Here's a custom renderer which puts a special class on links to external sites:

```php
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\Environment;
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;
use League\CommonMark\HtmlElement;

class MyCustomLinkRenderer implements InlineRendererInterface
{
    private $host;

    public function __construct($host)
    {
        $this->host = $host;
    }

    public function render(AbstractInline $inline, ElementRendererInterface $htmlRenderer)
    {
        if (!($inline instanceof Link)) {
            throw new \InvalidArgumentException('Incompatible inline type: ' . get_class($inline));
        }

        $attrs = array();

        $attrs['href'] = $htmlRenderer->escape($inline->getUrl(), true);

        if (isset($inline->attributes['title'])) {
            $attrs['title'] = $htmlRenderer->escape($inline->data['title'], true);
        }

        if ($this->isExternalUrl($inline->getUrl())) {
            $attrs['class'] = 'external-link';
        }

        return new HtmlElement('a', $attrs, $htmlRenderer->renderInlines($inline->children()));
    }

    private function isExternalUrl($url)
    {
        return parse_url($url, PHP_URL_HOST) !== $this->host;
    }
}

$environment = Environment::createCommonMarkEnvironment();
$environment->addInlineRenderer(Link::class, new MyCustomLinkRenderer());
```

## Tips

- Return an `HtmlElement` if possible. This makes it easier to extend and modify the results later.
- Some inlines can contain other inlines - don't forget to render those too!
