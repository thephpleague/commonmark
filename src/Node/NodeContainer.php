<?php

namespace League\CommonMark\Node;

abstract class NodeContainer extends Node
{
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
        for ($current = $this->firstChild;null !== $current;$current = $current->next) {
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
}
