---
layout: default
title: Block Rendering
redirect_from: /customization/block-rendering/
---

Block Rendering
===============

Block renderers are responsible for converting the parsed AST elements into their HTML representation.

All block renderers should implement `BlockRendererInterface` and its `render()` method:

## render()

The `HtmlRenderer` will call this method whenever a supported block element is encountered in the AST being rendered.

If the method can only handle certain block types, be sure to verify that you've been passed the correct type.

### Parameters

* `AbstractBlock $block` - The encountered block you must render
* `ElementRendererInterface $htmlRenderer` - The AST renderer; use this to render inlines or easily generate HTML tags
* `$inTightList = false` - Whether the element is being rendered in a tight list or not

### Return value

The method must return the final HTML representation of the block and any of its contents. This can be an `HtmlElement` object (preferred; castable to a string) or a string of raw HTML.

You are responsible for handling any escaping that may be necessary.

## Designating Block Renderers

When registering your render, you must tell the `Environment` which block element class your renderer should handle. For example:

~~~php
<?php

$environment = Environment::createCommonMarkEnvironment();

// First param - the block class type that should use our renderer
// Second param - instance of the block renderer
$environment->addBlockRenderer('League\CommonMark\Block\Element\FencedCode', new MyCustomCodeRenderer());
~~~

A single renderer could even be used for multiple block types:

~~~php
<?php

$environment = Environment::createCommonMarkEnvironment();

$myRenderer = new MyCustomCodeRenderer();

$environment->addBlockRenderer('League\CommonMark\Block\Element\FencedCode', $myRenderer);
$environment->addBlockRenderer('League\CommonMark\Block\Element\IndentedCode', $myRenderer);
~~~

Only one renderer can be assigned per element type.  If multiple renderers are assigned to the same block class, only the last one will be used.

## Example

Here's a custom renderer which renders thematic breaks as text (instead of `<hr>`):

~~~php
<?php

class TextDividerRenderer implements BlockRendererInterface
{
    public function render(AbstractBlock $block, ElementRendererInterface $htmlRenderer, $inTightList = false)
    {
        return new HtmlElement('pre', ['class' => 'divider'], '==============================');
    }
}

$environment = Environment::createCommonMarkEnvironment();
$environment->addBlockRenderer('League\CommonMark\Block\Element\ThematicBreak', new TextDividerRenderer());
~~~

## Tips

* Return an `HtmlElement` if possible. This makes it easier to extend and modify the results later.
* Don't forget to render any inlines your block might contain!
