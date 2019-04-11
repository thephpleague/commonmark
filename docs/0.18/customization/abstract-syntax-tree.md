---
layout: default
title: Abstract Syntax Tree
---

Abstract Syntax Tree
====================

Starting with version 0.11, this library uses a doubly-linked list AST.  Every element (both blocks and inlines) extend from the `Node` class.

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

The best way to manipulate the AST is by implementing a custom Document Processor.  These are executed once all other processing is done and the document is ready to be rendered. Simply create a class which implements the `DocumentProcessorInterface` which contains a single method:

~~~php
<?php

/**
 * @param Document $document
 *
 * @return void
 */
public function processDocument(Document $document);
~~~

This method receives the root `Document` node which you could then walk across, modifying nodes as needed.

Here's an example of a Document Processor which adds an `external-link` class to external URLs.
 
~~~php
<?php

class ExternalLinkProcessor implements DocumentProcessorInterface, ConfigurationAwareInterface
{
    private $config;

    /**
     * @param Configuration $configuration
     */
    public function setConfiguration(Configuration $configuration)
    {
        $this->config = $configuration;
    }

    /**
     * @param Document $document
     *
     * @return void
     */
    public function processDocument(Document $document)
    {
        $walker = $document->walker();
        while ($event = $walker->next()) {
            $node = $event->getNode();

            // Only stop at Link nodes when we first encounter them
            if (!($node instanceof Link) || !$event->isEntering()) {
                continue;
            }

            $url = $node->getUrl();
            if ($this->isUrlExternal($url)) {
                $node->data['attributes']['class'] = 'external-link';
            }
        }
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    private function isUrlExternal($url)
    {
        // Only look at http and https URLs
        if (!preg_match('/^https?:\/\//', $url)) {
            return false;
        }

        $host = parse_url($url, PHP_URL_HOST);

        return $host != $this->config->getConfig('host');
    }
}
~~~

And here's how you'd use it:

~~~php
<?php

$env = Environment::createCommonMarkEnvironment();
$env->addDocumentProcessor(new ExternalLinkProcessor());

$converter = new CommonMarkConverter(['host' => 'commonmark.thephpleague.com'], $env);

$input = 'My two favorite sites are <http://google.com> and <http://commonmark.thephpleague.com>';

echo $converter->convertToHtml($input);
~~~

Output (formatted for readability):

~~~html
<p>
    My two favorite sites are
    <a class="external-link" href="http://google.com">http://google.com</a>
    and
    <a href="http://commonmark.thephpleague.com">http://commonmark.thephpleague.com</a>
</p>
~~~
