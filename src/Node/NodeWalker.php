<?php

namespace League\CommonMark\Node;

class NodeWalker
{

    private $root;

    private $current;

    private $entering;

    public function __construct(Node $root)
    {
        $this->root = $root;
        $this->current = $this->root;
        $this->entering = true;
    }

    /**
     * @return NodeWalkerEvent|null
     */
    public function next()
    {
        $current = $this->current;
        if (null === $current) {
            return;
        }

        if ($this->entering && $current->isContainer()) {
            if ($current->getFirstChild()) {
                $this->current = $current->getFirstChild();
                $this->entering = true;
            } else {
                $this->entering = false;
            }
        } elseif ($current === $this->root) {
            $this->current = null;
        } elseif (null === $current->getNext()) {
            $this->current = $current->getParent();
            $this->entering = false;
        } else {
            $this->current = $current->getNext();
            $this->entering = true;
        }

        return new NodeWalkerEvent($current, $this->entering);
    }

    public function resumeAt(Node $node, $entering = true)
    {
        $this->current = $node;
        $this->entering = $entering;
    }

}