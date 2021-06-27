<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Node;

/**
 * @implements \IteratorAggregate<int, Node>
 */
final class NodeIterator implements \IteratorAggregate
{
    private Node $node;

    public function __construct(Node $node, int $flags = 0)
    {
        $this->node = $node;
    }

    /**
     * @return \Generator<int, Node>
     */
    public function getIterator(): \Generator
    {
        $stack = [$this->node];
        $index = 0;

        while ($stack) {
            $node = \array_pop($stack);

            yield $index++ => $node;

            // Push all children onto the stack in reverse order
            $child = $node->lastChild();
            while ($child !== null) {
                \array_push($stack, $child);
                $child = $child->previous();
            }
        }
    }
}
