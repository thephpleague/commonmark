<?php

namespace League\CommonMark\Node;

use League\CommonMark\Cursor;

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
     * @var int
     */
    protected $startLine;

    /**
     * @var int
     */
    protected $endLine;

    /**
     * @var bool
     */
    protected $lastLineBlank = false;

    /**
     * @return Node|null
     */
    public function getPrevious()
    {
        return $this->previous;
    }

    /**
     * @return Node|null
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * @return Node|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Node $sibling
     */
    public function insertAfter(Node $sibling)
    {
        $sibling->unlink();
        $sibling->next = $this->next;

        if ($sibling->next) {
            $sibling->next->previous = $sibling;
        }

        $sibling->previous = $this;
        $this->next = $sibling;
        $sibling->parent = $this->parent;

        if (!$sibling->next) {
            $sibling->parent->lastChild = $sibling;
        }
    }

    /**
     * @param Node $sibling
     */
    public function insertBefore(Node $sibling)
    {
        $sibling->unlink();
        $sibling->previous = $this->previous;

        if ($sibling->previous) {
            $sibling->previous->next = $sibling;
        }

        $sibling->next = $this;
        $this->previous = $sibling;
        $sibling->parent = $this->parent;

        if (!$sibling->previous) {
            $sibling->parent->firstChild = $sibling;
        }
    }

    public function replaceWith(Node $replacement)
    {
        $replacement->unlink();
        $this->insertAfter($replacement);
        $this->unlink();
    }

    public function unlink()
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
     * @return Node|null
     */
    public function getFirstChild()
    {
        return $this->firstChild;
    }

    /**
     * @return Node|null
     */
    public function getLastChild()
    {
        return $this->lastChild;
    }

    /**
     * @return Node[]
     */
    public function getChildren()
    {
        $children = [];
        for($current = $this->firstChild;null !== $current;$current = $current->next) {
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
            $child->unlink();
            $child->parent = $this;
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
            $child->unlink();
            $child->parent = $this;
            $this->lastChild = $this->firstChild = $child;
        }
    }

    public function unlinkChildren()
    {
        foreach ($this->getChildren() as $children) {
            $children->parent = null;
        }
        $this->firstChild = $this->lastChild = null;
    }

}
