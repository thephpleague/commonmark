<?php

namespace League\CommonMark\Node;

class NodeWalker
{
    /**
     * @var Node
     */
    private $root;

    /**
     * @var Node
     */
    private $current;

    /**
     * @var bool
     */
    private $entering;

    /**
     * @param Node $root
     */
    public function __construct(Node $root)
    {
        $this->root = $root;
        $this->current = $this->root;
        $this->entering = true;
    }

    /**
     * Returns an event which contains node and entering flag
     * (entering is true when we enter a Node from a parent or sibling,
     * and false when we reenter it from child)
     *
     * @return NodeWalkerEvent|null
     */
    public function next()
    {
        $current = $this->current;
        $entering = $this->entering;
        if (null === $current) {
            return;
        }

        if ($entering && $current->isContainer()) {
            if ($current->firstChild()) {
                $this->current = $current->firstChild();
                $this->entering = true;
            } else {
                $this->entering = false;
            }
        } elseif ($current === $this->root) {
            $this->current = null;
        } elseif (null === $current->next()) {
            $this->current = $current->parent();
            $this->entering = false;
        } else {
            $this->current = $current->next();
            $this->entering = true;
        }

        return new NodeWalkerEvent($current, $entering);
    }

    /**
     * Resets the iterator to resume at the specified node
     *
     * @param Node $node
     * @param bool $entering
     */
    public function resumeAt(Node $node, $entering = true)
    {
        $this->current = $node;
        $this->entering = $entering;
    }
}
