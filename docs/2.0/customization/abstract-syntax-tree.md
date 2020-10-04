---
layout: default
title: Abstract Syntax Tree
description: Using the Abstract Syntax Tree (AST) to manipulate the parsed content
---

Abstract Syntax Tree
====================

This library uses a doubly-linked list Abstract Syntax Tree (AST) to represent the parsed block and inline elements.  All such elements extend from the `Node` class.

## `Document`

The root node of the AST will always be a `Document` object.  You can obtain this node a few different ways:

 - By calling the `parse()` method on the `MarkdownParser`
 - By calling the `getDocument()` method on either the `DocumentPreParsedEvent` or `DocumentParsedEvent` (see the (Event Dispatcher documentation)[/2.0/customization/event-dispatcher/])

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

use League\CommonMark\Node\NodeWalker;

/** @var NodeWalker $walker */
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

## `DocumentParsedEvent`

The best way to access and manipulate the AST is by adding an [event listener](/2.0/customization/event-dispatcher/) for the `DocumentParsedEvent`.

## Data Storage

Each `Node` has a property called `data` which is a `Data` (array-like) object.  This can be used to store any arbitrary data you'd like on the node:

```php
use League\CommonMark\Node\Inline\Text;

$text1 = new Text('Hello, world!');
$text1->data->set('language', 'English');
$text1->data->set('is_good_translation', true);

$text2 = new Text('Bonjour monde!');
$text2->data->set('language', 'French');
$text2->data->set('is_good_translation', false);

foreach ([$text1, $text2] as $text) {
    if ($text->data->get('is_good_translation')) {
        sprintf('In %s we would say: "%s"', $text->data->get('language'), $text->getLiteral());
    } else {
        sprintf('I think they would say "%s" in %s, but I\'m not sure.', $text->getLiteral(), $text->data->get('language'));
    }
}
```

You can also access deeply-nested paths using `/` or `.` as delimiters:

```php
use League\CommonMark\Node\Inline\Text;

$text = new Text('Hello, world!');
$text->data->set('info', ['language' => 'English', 'is_good_translation' => true]);

var_dump($text->data->get('info/language'));
var_dump($text->data->get('info.is_good_translation'));

$text->data->set('info/is_example', true);
```

### HTML Attributes

The `data` property comes pre-instantiated with a single data element called `attributes` which is used to store any HTML attributes that need to be rendered.  For example:

```php
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;

$link = new Link('https://twitter.com/colinodell', '@colinodell');
$link->data->append('attributes/class', 'social-link');
$link->data->append('attributes/class', 'twitter');
$link->data->set('attributes/target', '_blank');
$link->data->set('attributes/rel', 'noopener');
```
