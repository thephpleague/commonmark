<?php

declare(strict_types=1);

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

namespace League\CommonMark\Tests\Unit\Renderer;

use League\CommonMark\Renderer\ChildNodeRendererInterface;

final class FakeChildNodeRenderer implements ChildNodeRendererInterface
{
    private bool $alwaysOutputChildren = false;

    public function pretendChildrenExist(): void
    {
        $this->alwaysOutputChildren = true;
    }

    /**
     * {@inheritDoc}
     */
    public function renderNodes(iterable $nodes): string
    {
        if ($this->alwaysOutputChildren) {
            return '::children::';
        }

        // Only return '::children::' if the iterable isn't empty
        foreach ($nodes as $node) {
            return '::children::';
        }

        return '';
    }

    public function getBlockSeparator(): string
    {
        return '';
    }

    public function getInnerSeparator(): string
    {
        return '';
    }
}
