---
layout: default
title: Attributes Extension
description: The AttributesExtension allows HTML attributes to be added from within the document.
---

# Attributes

The `AttributesExtension` allows HTML attributes to be added from within the document.

## Attribute Syntax

The basic syntax was inspired by [Kramdown](http://kramdown.gettalong.org/syntax.html#attribute-list-definitions)'s Attribute Lists feature.

You can assign any attribute to a block-level element. Just directly prepend or follow the block with a block inline attribute list.
That consists of a left curly brace, optionally followed by a colon, the attribute definitions and a right curly brace:

```markdown
> A nice blockquote
{: title="Blockquote title"}
```

This results in the following output:

```html
<blockquote title="Blockquote title">
<p>A nice blockquote</p>
</blockquote>
```

CSS-selector-style declarations can be used to set the `id` and `class` attributes:

```markdown
{#id .class}
## Header
```

Output:

```html
<h2 class="class" id="id">Header</h2>
```

As with a block-level element you can assign any attribute to a span-level elements using a span inline attribute list,
that has the same syntax and must immediately follow the span-level element:

```markdown
This is *red*{style="color: red"}.
```

Output:

```html
<p>This is <em style="color: red">red</em>.</p>
```

### Empty-Value Attributes

Attributes can be rendered in HTML without a value by using `true` value in the markdown document:

```markdown
{itemscope=true}
## Header
```

Output:

```html
<h2 itemscope>Header</h2>
```

## Installation

This extension is bundled with `league/commonmark`. This library can be installed via Composer:

```bash
composer require league/commonmark
```

See the [installation](/2.5/installation/) section for more details.

## Usage

Configure your `Environment` as usual and simply add the `AttributesExtension`:

```php
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

// Define your configuration, if needed
$config = [];

// Configure the Environment with all the CommonMark parsers/renderers
$environment = new Environment($config);
$environment->addExtension(new CommonMarkCoreExtension());

// Add this extension
$environment->addExtension(new AttributesExtension());

// Instantiate the converter engine and start converting some Markdown!
$converter = new MarkdownConverter($environment);
echo $converter->convert('# Hello World!');
```
