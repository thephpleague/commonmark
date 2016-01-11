<?php

namespace League\CommonMark\Node;

use League\CommonMark\Util\ArrayCollection;

abstract class Node
{
    /**
     * @var Node|null
     */
    protected $parent;

    /**
     * @var Node|null
     */
    protected $previous;

    /**
     * @var Node|null
     */
    protected $next;

    /**
     * @var Node|null
     */
    protected $firstChild;

    /**
     * @var Node|null
     */
    protected $lastChild;

    /**
     * @return Node|null
     */
    public function previous()
    {
        return $this->previous;
    }

    /**
     * @return Node|null
     */
    public function next()
    {
        return $this->next;
    }

    /**
     * @return Node|null
     */
    public function parent()
    {
        return $this->parent;
    }

    /**
     * @param Node|null $node
     */
    protected function setParent(Node $node = null)
    {
        $this->parent = $node;
    }

    /**
     * @param Node $sibling
     */
    public function insertAfter(Node $sibling)
    {
        $sibling->detach();
        $sibling->next = $this->next;

        if ($sibling->next) {
            $sibling->next->previous = $sibling;
        }

        $sibling->previous = $this;
        $this->next = $sibling;
        $sibling->setParent($this->parent);

        if (!$sibling->next) {
            $sibling->parent->lastChild = $sibling;
        }
    }

    /**
     * @param Node $sibling
     */
    public function insertBefore(Node $sibling)
    {
        $sibling->detach();
        $sibling->previous = $this->previous;

        if ($sibling->previous) {
            $sibling->previous->next = $sibling;
        }

        $sibling->next = $this;
        $this->previous = $sibling;
        $sibling->setParent($this->parent);

        if (!$sibling->previous) {
            $sibling->parent->firstChild = $sibling;
        }
    }

    public function replaceWith(Node $replacement)
    {
        $replacement->detach();
        $this->insertAfter($replacement);
        $this->detach();
    }

    public function detach()
    {
        if ($this->previous) {
            $this->previous->next = $this->next;
        } elseif ($this->parent) {
            $this->parent->firstChild = $this->next;
        }

        if ($this->next) {
            $this->next->previous = $this->previous;
        } elseif ($this->parent) {
            $this->parent->lastChild = $this->previous;
        }

        $this->parent = null;
        $this->next = null;
        $this->previous = null;
    }

    /**
     * @return bool
     */
    abstract public function isContainer();

    /**
     * @return Node|null
     */
    public function firstChild()
    {
        return $this->firstChild;
    }

    /**
     * @return Node|null
     */
    public function lastChild()
    {
        return $this->lastChild;
    }

    /**
     * @return Node[]
     */
    public function children()
    {
        $children = [];
        for ($current = $this->firstChild; null !== $current; $current = $current->next) {
            $children[] = $current;
        }

        return $children;
    }

    /**
     * @param Node $child
     */
    public function appendChild(Node $child)
    {
        if ($this->lastChild) {
            $this->lastChild->insertAfter($child);
        } else {
            $child->detach();
            $child->setParent($this);
            $this->lastChild = $this->firstChild = $child;
        }
    }

    /**
     * @param Node $child
     */
    public function prependChild(Node $child)
    {
        if ($this->firstChild) {
            $this->firstChild->insertBefore($child);
        } else {
            $child->detach();
            $child->setParent($this);
            $this->lastChild = $this->firstChild = $child;
        }
    }

    /**
     * Detaches all child nodes of given node
     */
    public function detachChildren()
    {
        foreach ($this->children() as $children) {
            $children->setParent(null);
        }
        $this->firstChild = $this->lastChild = null;
    }

    /**
     * Replace all children of given node with collection of another
     *
     * @param array $children
     *
     * @return $this
     */
    public function replaceChildren(array $children)
    {
        if (!is_array($children) && !(is_object($children) && $children instanceof ArrayCollection)) {
            throw new \InvalidArgumentException(sprintf('Expect iterable, got %s', get_class($children)));
        }

        $this->detachChildren();
        foreach ($children as $item) {
            $this->appendChild($item);
        }

        return $this;
    }

    /**
     * @return NodeWalker
     */
    public function walker()
    {
        return new NodeWalker($this);
    }
}
