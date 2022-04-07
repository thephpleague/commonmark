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
- By calling the `getDocument()` method on either the `DocumentPreParsedEvent` or `DocumentParsedEvent` [see the (Event Dispatcher documentation](/2.1/customization/event-dispatcher/))

## Visualization

Even with an interactive debugger it can be tricky to view an entire tree at once.  Consider using the [`XmlRenderer`](/2.1/xml/) to provide a simple text-based representation of the AST for debugging purposes.

## Node Traversal

There are four different ways to traverse/iterate the Nodes within the AST:

| Method | Pros | Cons |
| --- | --- | --- |
| Manual Traversal | Best way to access/check direct relatives of nodes | Not useful for iteration |
| Iterating the Tree | Fast and efficient | Possible unexpected behavior when adding/removing sibling nodes while iterating |
| Walking the Tree | Full control over iteration | Up to twice as slow as iteration; adding/removing nodes while iterating can lead to weird behaviors |
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

### Iterating the Tree

If you'd like to iterate through all the nodes, use the `iterator()` method to obtain an iterator that will loop through each node in the tree (using pre-order traversal):

```php
foreach ($document->iterator() as $node) {
    echo 'Current node: ' . get_class($node) . "\n";
}
```

Given an AST like this (XML representation):

```xml
<document>
  <heading level="1">
    <text>Hello World!</text>
  </heading>
  <paragraph>
    <text>This is an example of </text>
    <strong>
      <text>CommonMark</text>
    </strong>
    <text>.</text>
  </paragraph>
</document>
```

The code above will output:

```text
Current node: League\CommonMark\Node\Block\Document
Current node: League\CommonMark\Extension\CommonMark\Node\Block\Heading
Current node: League\CommonMark\Node\Inline\Text
Current node: League\CommonMark\Node\Block\Paragraph
Current node: League\CommonMark\Node\Inline\Text
Current node: League\CommonMark\Extension\CommonMark\Node\Inline\Strong
Current node: League\CommonMark\Node\Inline\Text
Current node: League\CommonMark\Node\Inline\Text
```

This iterator doesn't use recursion, so you won't blow the stack when working with deeply-nested nodes.  It's also very CPU and memory-efficient.

Be careful when modifying nodes while iterating the tree as some of those changes may affect the current iteration process, especially for sibling nodes that come after the current one.  For example, if you remove the current node's `next()` sibling, the next loop of that iteration will still include the removed sibling even though it was successfully removed from the AST.  Similarly, any new siblings that are added won't be visited on the next loop.

### Walking the Tree

If you'd like to walk through all the nodes, visiting each one as you enter and leave it, use the `walker()` method to obtain an instance of `NodeWalker`.  This also uses pre-order traversal but emitting `NodeWalkerEvent`s along the way:

```php
use League\CommonMark\Node\NodeWalker;

/** @var NodeWalker $walker */
$walker = $document->walker();
while ($event = $walker->next()) {
    echo 'Now ' . ($event->isEntering() ? 'entering' : 'leaving') . ' a ' . get_class($event->getNode()) . ' node' . "\n";
}
```

Using the same example AST in the previous section, this code will output:

```text
Now entering a League\CommonMark\Node\Block\Document node
Now entering a League\CommonMark\Extension\CommonMark\Node\Block\Heading node
Now entering a League\CommonMark\Node\Inline\Text node
Now leaving a League\CommonMark\Extension\CommonMark\Node\Block\Heading node
Now entering a League\CommonMark\Node\Block\Paragraph node
Now entering a League\CommonMark\Node\Inline\Text node
Now entering a League\CommonMark\Extension\CommonMark\Node\Inline\Strong node
Now entering a League\CommonMark\Node\Inline\Text node
Now leaving a League\CommonMark\Extension\CommonMark\Node\Inline\Strong node
Now entering a League\CommonMark\Node\Inline\Text node
Now leaving a League\CommonMark\Node\Block\Paragraph node
Now leaving a League\CommonMark\Node\Block\Document node
```

This approach offers many of the same benefits as the simple iteration shown in the previous section such as memory efficiency and no recursion.  The key differences come from how you enter and leave nodes:

1. Iteration can potentially take twice as long - not ideal for performance
2. Provides you with more control over exactly when an action is taken on a node which is sometimes needed for certain AST manipulations
3. Also provides a `resumeAt()` method to override where it should iterate next

But like with the iterator, be careful when adding/removing nodes while walking the tree, as there are even more subtle cases where the walker could even lose track of where it was, which may result in some nodes being visited multiple times or not at all.

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

The best way to access and manipulate the AST is by adding an [event listener](/2.1/customization/event-dispatcher/) for the `DocumentParsedEvent`.

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
