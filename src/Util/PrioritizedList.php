<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Util;

/**
 * @internal
 */
final class PrioritizedList implements \IteratorAggregate
{
    private $list = [];

    private $optimized;

    /**
     * @param mixed $item
     * @param int   $priority
     */
    public function add($item, int $priority)
    {
        $this->list[$priority][] = $item;
        $this->optimized = null;
    }

    /**
     * @return \Traversable
     */
    public function getIterator(): iterable
    {
        if ($this->optimized === null) {
            \krsort($this->list);

            $sorted = [];
            foreach ($this->list as $group) {
                foreach ($group as $item) {
                    $sorted[] = $item;
                }
            }

            $this->optimized = new \ArrayIterator($sorted);
        }

        return $this->optimized;
    }
}
