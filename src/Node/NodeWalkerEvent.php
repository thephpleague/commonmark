<?php

namespace League\CommonMark\Node;

final class NodeWalkerEvent
{
    /**
     * @var Node
     */
    private $node;

    /**
     * @var bool
     */
    private $isEntering;

    /**
     * @param Node $node
     * @param bool $isEntering
     */
    public function __construct(Node $node = null, $isEntering = true)
    {
        $this->node = $node;
        $this->isEntering = $isEntering;
    }

    /**
     * @return Node
     */
    public function getNode(): Node
    {
        return $this->node;
    }

    /**
     * @return bool
     */
    public function isEntering(): bool
    {
        return $this->isEntering;
    }
}
