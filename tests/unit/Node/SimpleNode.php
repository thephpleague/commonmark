<?php

namespace League\CommonMark\Tests\Unit\Node;

use League\CommonMark\Node\Node;

/**
 * A simple node used for testing purposes.
 */
final class SimpleNode extends Node
{
    /**
     * @var bool
     */
    private $container;

    /**
     * @param bool $isContainer
     */
    public function __construct($isContainer = true)
    {
        $this->container = $isContainer;
    }

    /**
     * @return bool
     */
    public function isContainer()
    {
        return $this->container;
    }
}
