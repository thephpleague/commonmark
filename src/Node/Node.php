<?php

namespace League\CommonMark\Node;

use League\CommonMark\Util\ArrayCollection;

abstract class Node
{
    /**
     * @var NodeContainer|null
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
     * @var array
     *
     * Used for storage of arbitrary data
     */
    public $data = [];

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getData($key, $default = null)
    {
        return array_key_exists($key, $this->data) ? $this->data[$key] : $default;
    }

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
     * @return NodeContainer|null
     */
    public function parent()
    {
        return $this->parent;
    }

    protected function setParent(NodeContainer $node)
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
     * @return NodeWalker
     */
    public function walker()
    {
        return new NodeWalker($this);
    }
}
