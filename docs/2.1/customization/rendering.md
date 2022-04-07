---
layout: default
title: Rendering
description: How to customize the rendering of block and inline elements
---

# Custom Rendering

Renderers are responsible for converting the parsed AST elements into their HTML representation.

All block renderers should implement `NodeRendererInterface` and its `render()` method.  Note that in v2.0, both
block renderers and inline renderers share the same interface and method:

## render()

```php
public function render(Node $node, ChildNodeRendererInterface $childRenderer);
```

The `HtmlRenderer` will call this method during the rendering process whenever a supported element is encountered.

If your renderer can only handle certain block types, be sure to verify that you've been passed the correct type.

### Parameters

- `Node $node` - The encountered block or inline element that needs to be rendered
- `ChildNodeRendererInterface $childRenderer` - If the given $node has children, use this to render those child elements

### Return value

The method must return the final HTML representation of the node and its contents, including any children. This can be an `HtmlElement` object (preferred; castable to a string), a string of raw HTML, or `null` if it could not render (and perhaps another renderer should give it a try).

If you choose to return an HTML `string` you are responsible for handling any escaping that may be necessary.

#### `HtmlElement`

Instead of manually building the HTML output yourself, you can leverage the `HtmlElement` to generate that for you.  For example:

```php
use League\CommonMark\Util\HtmlElement;

$link = new HtmlElement('a', ['href' => 'https://github.com'], 'GitHub');
$img = new HtmlElement('img', ['src' => 'logo.jpg'], '', true);
```

## Designating Renderers

When registering your renderer, you must tell the `Environment` which node element class your renderer should handle. For example:

```php
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;

$environment = new Environment();
$environment->addExtension(new CommonMarkCoreExtension());

// First param - the node class type that should use our renderer
// Second param - instance of the renderer
$environment->addRenderer(FencedCode::class, new MyCustomCodeRenderer());
```

A single renderer could even be used for multiple types:

```php
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode;

$environment = new Environment();
$environment->addExtension(new CommonMarkCoreExtension());

$myRenderer = new MyCustomCodeRenderer();

$environment->addRenderer(FencedCode::class, $myRenderer, 10);
$environment->addRenderer(IndentedCode::class, $myRenderer, 20);
```

Multiple renderers can be added per element type - when this happens, we use the result from the highest-priority renderer that returns a non-`null` result.

## Example

Here's a custom renderer which renders thematic breaks as text (instead of `<hr>`):

```php
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\ThematicBreak;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

class TextDividerRenderer implements NodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer)
    {
        return new HtmlElement('pre', ['class' => 'divider'], '==============================');
    }
}

$environment = new Environment();
$environment->addExtension(new CommonMarkCoreExtension());
$environment->addRenderer(ThematicBreak::class, new TextDividerRenderer());
```

Note that thematic breaks should not contain children, which is why the `$childRenderer` is unused in this example.  Otherwise we'd have to call code like this and return the result as part of the rendered HTML we're generating here: `$innerHtml = $childRenderer->renderNodes($node->children());`

## Tips

- Return an `HtmlElement` if possible. This makes it easier to extend and modify the results later.
- Don't forget to render any child elements that your node might contain!

## XML Rendering

The [XML renderer](/2.1/xml/) will automatically attempt to convert any AST nodes to XML by inspecting the name of the block/inline node and its attributes. You can instead control the XML element name and attributes by making your renderer implement `XmlNodeRendererInterface`:

```php
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use League\CommonMark\Xml\XmlNodeRendererInterface;

class TextDividerRenderer implements NodeRendererInterface, XmlNodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer)
    {
        return new HtmlElement('pre', ['class' => 'divider'], '==============================');
    }

    public function getXmlTagName(Node $node): string
    {
        return 'text_divider';
    }

    public function getXmlAttributes(Node $node): array
    {
        return ['character' => '='];
    }
}
```
