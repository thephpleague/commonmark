---
layout: default
title: Block Rendering
description: How to customize the rendering of block-level elements
---

# Block Rendering

Block renderers are responsible for converting the parsed AST elements into their HTML representation.

All block renderers should implement `BlockRendererInterface` and its `render()` method:

## render()

```php
public function render(AbstractBlock $block, ElementRendererInterface $htmlRenderer, bool $inTightList = false);
```

The `HtmlRenderer` will call this method whenever a supported block element is encountered in the AST being rendered.

If the method can only handle certain block types, be sure to verify that you've been passed the correct type.

### Parameters

- `AbstractBlock $block` - The encountered block you must render
- `ElementRendererInterface $htmlRenderer` - The AST renderer; use this to render inlines or easily generate HTML tags
- `$inTightList = false` - Whether the element is being rendered in a tight list or not

### Return value

The method must return the final HTML representation of the block and any of its contents. This can be an `HtmlElement` object (preferred; castable to a string), a string of raw HTML, or `null` if it could not render (and perhaps another renderer should give it a try).

If you choose to return an HTML `string` you are responsible for handling any escaping that may be necessary.

#### `HtmlElement`

Instead of manually building the HTML output yourself, you can leverage the `HtmlElement` to generate that for you.  For example:

```php
use League\CommonMark\HtmlElement;

$link = new HtmlElement('a', ['href' => 'https://github.com'], 'GitHub');
$img = new HtmlElement('img', ['src' => 'logo.jpg'], '', true);
```

## Designating Block Renderers

When registering your renderer, you must tell the `Environment` which block element class your renderer should handle. For example:

```php
use League\CommonMark\Block\Element\FencedCode;
use League\CommonMark\Environment;

$environment = Environment::createCommonMarkEnvironment();

// First param - the block class type that should use our renderer
// Second param - instance of the block renderer
$environment->addBlockRenderer(FencedCode::class, new MyCustomCodeRenderer());
```

A single renderer could even be used for multiple block types:

```php
use League\CommonMark\Block\Element\FencedCode;
use League\CommonMark\Block\Element\IndentedCode;
use League\CommonMark\Environment;

$environment = Environment::createCommonMarkEnvironment();

$myRenderer = new MyCustomCodeRenderer();

$environment->addBlockRenderer(FencedCode::class, $myRenderer, 10);
$environment->addBlockRenderer(IndentedCode::class, $myRenderer, 20);
```

Multiple renderers can be added per element type - when this happens, we use the result from the highest-priority renderer that returns a non-`null` result.

## Example

Here's a custom renderer which renders thematic breaks as text (instead of `<hr>`):

```php
use League\CommonMark\Environment;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Renderer\Block\BlockRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

class TextDividerRenderer implements BlockRendererInterface
{
    public function render(AbstractBlock $block, NodeRendererInterface $htmlRenderer, bool $inTightList = false)
    {
        return new HtmlElement('pre', ['class' => 'divider'], '==============================');
    }
}

$environment = Environment::createCommonMarkEnvironment();
$environment->addBlockRenderer('League\CommonMark\Block\Element\ThematicBreak', new TextDividerRenderer());
```

## Tips

- Return an `HtmlElement` if possible. This makes it easier to extend and modify the results later.
- Don't forget to render any inlines your block might contain!
