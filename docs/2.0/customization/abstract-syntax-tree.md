---
layout: default
title: Abstract Syntax Tree
description: Using the Abstract Syntax Tree (AST) to manipulate the parsed content
---

# Abstract Syntax Tree

This library uses a doubly-linked list Abstract Syntax Tree (AST) to represent the parsed block and inline elements.  All such elements extend from the `Node` class.

## `Document`

The root node of the AST will always be a `Document` object.  You can obtain this node a few different ways:

- By calling the `parse()` method on the `MarkdownParser`
- By calling the `getDocument()` method on either the `DocumentPreParsedEvent` or `DocumentParsedEvent` [see the (Event Dispatcher documentation](/2.0/customization/event-dispatcher/))

## Node Traversal

There are three different ways to traverse/iterate the Nodes within the AST:

| Method | Pros | Cons |
| --- | --- | --- |
| Manual Traversal | Best way to access/check direct relatives of nodes | Not useful for iteration |
| Walking the Tree | Fast and efficient | Adding/removing nodes while iterating them can lead to weird behaviors |
| Querying Nodes | Easier to write and understand; no weird behaviors | Not memory efficient |

Each is described in more detail below

### Manual Traversal

The following methods can be used to manually traverse from one `Node` to any of its direct relatives:

- `previous()`
- `next()`
- `parent()`
- `firstChild()`
- `lastChild()`
- `children()`

This is best suited for situations when you need to know information about those relatives.

### Walking the Tree

If you'd like to iterate through all the nodes, use the `walker()` method to obtain an instance of `NodeWalker`.  This will walk through the entire tree, emitting `NodeWalkerEvent`s along the way.

```php
use League\CommonMark\Node\NodeWalker;

/** @var NodeWalker $walker */
$walker = $document->walker();
while ($event = $walker->next()) {
    echo 'I am ' . ($event->isEntering() ? 'entering' : 'leaving') . ' a ' . get_class($event->getNode()) . ' node' . "\n";
}
```

This walker doesn't use recursion, so you won't blow the stack when working with deeply-nested nodes.  It's also very memory-efficient.

However, if you add/remove nodes while walking the tree, this can lead to the walker losing track of where it was, which may result in some nodes being visited multiple times or not at all.

### Querying Nodes

If you're trying to locate certain nodes to perform actions on them, querying the nodes from the AST might be easier to implement.  This can be done with the `Query` class:

```php
use League\CommonMark\Extension\CommonMark\Node\Block\BlockQuote;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Query;

// Find all paragraphs and blockquotes that contain links
$matchingNodes = (new Query())
    ->where(Query::type(Paragraph::class))
    ->orWhere(Query::type(BlockQuote::class))
    ->andWhere(Query::hasChild(Query::type(Link::class)))
    ->findAll($document);

foreach ($matchingNodes as $node) {
    // TODO: Do something with them
}
```

Each condition passed into `where()`, `orWhere()`, or `andWhere()` must be a callable "filter" that accepts a `Node` and returns `true` or `false`.  We provide several methods that can help create these filters for you:

| Method | Description |
| --- | --- |
| `Query::type(string $class)` | Creates a filter that matches nodes with the given class name |
| `Query::hasChild()` | Creates a filter that matches nodes which contain at least one child |
| `Query::hasChild(callable $condition)` | Creates a filter that matches nodes which contain at least one child that matches the inner `$condition` |
| `Query::hasParent()` | Creates a filter that matches nodes which have a parent |
| `Query::hasParent(callable $condition)` | Creates a filter that matches nodes which have a parent that matches the inner `$condition` |

You can of course create your own custom filters/conditions using an anonymous function or by implementing `ExpressionInterface`:

```php
use League\CommonMark\Node\Node;
use League\CommonMark\Node\Query;
use League\CommonMark\Node\Query\ExpressionInterface;

class ChildCountGreaterThan implements ExpressionInterface
{
    private $count;

    public function __construct(int $count)
    {
        $this->count = $count;
    }

    public function __invoke(Node $node) : bool{
        return count($node->children()) > $this->count;
    }
}

$query = (new Query())
    ->where(function (Node $node): bool { return $node->data->has('attributes/class'); })
    ->andWhere(new ChildCountGreaterThan(3));
```

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
