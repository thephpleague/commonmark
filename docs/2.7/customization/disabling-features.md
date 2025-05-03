---
layout: default
title: Disabling Features
description: How to disable certain features of this library
redirect_from:
- /customization/disabling-features/
---

# Disabling Features

The CommonMark parser is designed to be highly configurable.  You can disable certain features that you don't want to have in your application.  There are a few ways to do this, depending on your needs:

## Avoiding Parsing

You cannot disable an already-registered parser, but you can prevent it from being registered with
the [`Environment`](/2.7/customization/environment/) in the first place.  This is exactly how the
[`InlinesOnlyExtension`](/2.7/extensions/inlines-only/) works - it's a copy of the `CommonMarkCoreExtension` class but
with the parsers we don't want removed.

You can mirror this approach by defining your own [custom extension class](/2.7/customization/extensions/) that registers
only the specific parsers, renderers, etc. that you want.

The only potential downside to this approach is that any syntax for those disabled features will appear in the output.
For example, if you were to prevent block quotes from being parsed, then the following Markdown:

```markdown
> This is a block quote
```

Would have the `>` character appear in the output HTML:

```html
<p>&gt; This is a block quote</p>
```

This is probably fine for most use cases.

## Removing Parsed Elements

An alternative approach is to keep the parser enabled, but remove the parsed elements from the AST before rendering.

You'd create an [event listener](/2.7/customization/event-dispatcher/#registering-listeners)
(sort of like [this one](/2.7/customization/event-dispatcher/#example)) that will
[iterate all parsed elements](/2.7/customization/abstract-syntax-tree/), locate the target nodes, and remove them
by calling `$node->detach()`.

There are three potential advantages to this approach:

1. You don't need to create a custom extension class or prevent parsers from being registered
2. You can selectively remove certain elements based on their properties (e.g. only remove heading levels 3-6) while keeping others
3. The syntax and contents of the removed elements will not appear in the output HTML

The downside is that you still incur the overhead of parsing the elements that are eventually removed.

## Override Rendering

The final approach is to keep the parser enabled, but override how the parsed elements are rendered.  For example,
you could implement a [custom renderer](/2.7/customization/rendering/) for certain elements that simply returns
something else (perhaps an empty string, or an HTML comment of `<!-- REMOVED -->`) instead of the HTML you don't want.

This approach is not recommended because:

1. You still incur the overhead of parsing the elements that are eventually removed
2. You'd need to register your custom renderer with a higher priority than the default renderer
3. You'd need to repeat this for every renderer that could potentially render the elements you want to remove

It should technically work though, if you _really_ want to go this route.
