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

namespace League\CommonMark\Extension\TableOfContents\Node;

use League\CommonMark\Exception\InvalidArgumentException;
use League\CommonMark\Node\Block\AbstractBlock;

final class TableOfContentsWrapper extends AbstractBlock
{
    public function getInnerToc(): TableOfContents
    {
        $children = $this->children();
        if (! \is_array($children)) {
            /** @psalm-suppress NoValue */
            $children = \iterator_to_array($children);
        }

        if (\count($children) !== 2) {
            throw new InvalidArgumentException(
                'TableOfContentsWrapper nodes should have 2 children, found ' . \count($children)
            );
        }

        $inner = $children[1];
        if (! $inner instanceof TableOfContents) {
            throw new InvalidArgumentException(
                'TableOfContentsWrapper second node should be a TableOfContents, found ' . \get_class($inner)
            );
        }

        return $inner;
    }
}
