---
layout: default
title: Abstract Syntax Tree
description: Using the Abstract Syntax Tree (AST) to manipulate the parsed content
---

# Abstract Syntax Tree

This library uses a doubly-linked list Abstract Syntax Tree (AST) to represent the parsed block and inline elements.  All such elements extend from the `Node` class.

## Traversal

The following methods can be used to traverse the AST:

- `previous()`
- `next()`
- `parent()`
- `firstChild()`
- `lastChild()`
- `children()`

## Iteration / Walking the Tree

If you'd like to iterate through all the nodes, use the `walker()` method to obtain an instance of `NodeWalker`.  This will walk through the entire tree, emitting `NodeWalkerEvent`s along the way.

```php
use League\CommonMark\Node\NodeWalker;

/** @var NodeWalker $walker */
$walker = $document->walker();
while ($event = $walker->next()) {
    echo 'I am ' . ($event->isEntering() ? 'entering' : 'leaving') . ' a ' . get_class($event->getNode()) . ' node' . "\n";
}
```

This walker doesn't use recursion, so you won't blow the stack when working with deeply-nested nodes.

## Modification

The following methods can be used to modify the AST:

- `insertAfter(Node $sibling)`
- `insertBefore(Node $sibling)`
- `replaceWith(Node $replacement)`
- `detach()`
- `appendChild(Node $child)`
- `prependChild(Node $child)`
- `detachChildren()`
- `replaceChildren(Node[] $children)`

## `DocumentParsedEvent`

The best way to access and manipulate the AST is by adding an [event listener](/1.3/customization/event-dispatcher/) for the `DocumentParsedEvent`.
