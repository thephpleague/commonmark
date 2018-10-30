<?php

namespace League\CommonMark\Node;

final class NodeWalkerEvent
{
    /**
     * @var Node|null
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
     * @return Node|null
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @return bool
     */
    public function isEntering()
    {
        return $this->isEntering;
    }
}
