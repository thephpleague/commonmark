---
layout: default
title: XML Rendering
description: Rendering Markdown documents in XML
redirect_from:
  - /xml/
  - /2.0/xml/
  - /2.1/xml/
  - /2.2/xml/
  - /2.3/xml/
  - /2.4/xml/
  - /2.5/xml/
  - /2.6/xml/
  - /2.7/xml/
---

# XML Rendering

Version 2.0 introduced the ability to render Markdown `Document` objects in XML. This is particularly useful for debugging [custom extensions](/2.x/customization/overview/) as you can see the XML representation of the [Abstract Syntax Tree](/2.x/customization/abstract-syntax-tree/).

To convert Markdown to XML, you would instantiate a `MarkdownToXmlConverter` with an [`Environment`](/2.x/customization/environment/) and then call `convert()` on any Markdown.

```php
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Xml\MarkdownToXmlConverter;

$environment = new Environment();
$environment->addExtension(new CommonMarkCoreExtension());

$converter = new MarkdownToXmlConverter($environment);

echo $converter->convert('# **Hello** World!');
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

Alternatively, if you already have a `Document` object you want to visualize in XML, you can use the`XmlRenderer` class to convert it to XML.

## Return Value

Like with `CommonMarkConverter::convert()`, the `renderDocument()` actually returns an instance of `League\CommonMark\Output\RenderedContentInterface`.  You can cast this (implicitly, as shown above, or explicitly) to a `string` or call `getContent()` to get the final XML output.

## Indentation

Nested elements are indented by four spaces per level, up to the `xml/max_indentation_level` [configuration option](/2.x/configuration/) (default: `16`).  Elements nested more deeply than that are still rendered in full - they simply don't get indented any further, which keeps the size of the output proportional to the size of the document instead of growing quadratically with its depth.

Set this option to `0` if you'd prefer compact output with no indentation at all:

```php
$environment = new Environment(['xml' => ['max_indentation_level' => 0]]);
```

## Customizing the XML Output

See the [rendering documentation](/2.x/customization/rendering/#xml-rendering) for information on customizing the XML output.
