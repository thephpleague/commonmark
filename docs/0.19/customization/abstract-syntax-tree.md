---
layout: default
title: Abstract Syntax Tree
---

Abstract Syntax Tree
====================

This library uses a doubly-linked list Abstract Syntax Tree (AST) to represent the parsed block and inline elements.  All such element extend from the `Node` class.

## Traversal

The following methods can be used to traverse the AST:

* `previous()`
* `next()`
* `parent()`
* `firstChild()`
* `lastChild()`
* `children()`

## Iteration / Walking the Tree

If you'd like to iterate through all the nodes, use the `walker()` method to obtain an instance of `NodeWalker`.  This will walk through the entire tree, emitting `NodeWalkerEvent`s along the way.

~~~php
<?php

$walker = $document->walker();
while ($event = $walker->next()) {
    echo 'I am ' . ($event->isEntering() ? 'entering' : 'leaving') . ' a ' . get_class($event->getNode()) . ' node' . "\n";
}
~~~

This walker doesn't use recursion, so you won't blow the stack when working with deeply-nested nodes.

## Modification

The following methods can be used to modify the AST:

* `insertAfter(Node $sibling)`
* `insertBefore(Node $sibling)`
* `replaceWith(Node $replacement)`
* `detach()`
* `appendChild(Node $child)`
* `prependChild(Node $child)`
* `detachChildren()`
* `replaceChildren(Node[] $children)`

## Document Processor

The best way to manipulate the AST is by implementing a custom [Document Processor](/0.19/customization/document-processing/).
