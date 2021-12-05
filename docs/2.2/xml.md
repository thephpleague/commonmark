---
layout: default
title: XML Rendering
description: Rendering Markdown documents in XML
---

# XML Rendering

Version 2.0 introduced the ability to render Markdown `Document` objects in XML. This is particularly useful for debugging [custom extensions](/2.0/customization/overview/).

To convert Markdown to XML, you would instantiate an [`Environment`](/2.0/customization/environment/), parse the Markdown into an [AST](/2.0/customization/abstract-syntax-tree/), and render it using the new `XmlRenderer`:

```php
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Parser\MarkdownParser;
use League\CommonMark\Xml\XmlRenderer;

$environment = new Environment();
$environment->addExtension(new CommonMarkCoreExtension());

$parser = new MarkdownParser($environment);
$renderer = new XmlRenderer($environment);

$document = $parser->parse('# **Hello** World!');

echo $renderer->renderDocument($document);
```

This will display XML output like this:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<document xmlns="http://commonmark.org/xml/1.0">
    <heading level="1">
        <strong>
            <text>Hello</text>
        </strong>
        <text> World!</text>
    </heading>
</document>
```

## Return Value

Like with `CommonMarkConverter::convertToHtml()`, the `renderDocument()` actually returns an instance of `League\CommonMark\Output\RenderedContentInterface`.  You can cast this (implicitly, as shown above, or explicitly) to a `string` or call `getContent()` to get the final XML output.

## Customizing the XML Output

See the [rendering documentation](/2.0/customization/rendering/#xml-rendering) for information on customizing the XML output.
