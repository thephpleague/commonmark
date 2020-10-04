<?php

declare(strict_types=1);

/*
 * This is part of the league/commonmark package.
 *
 * (c) Martin Hasoň <martin.hason@gmail.com>
 * (c) Webuni s.r.o. <info@webuni.cz>
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\Table;

use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

final class TableSectionRenderer implements NodeRendererInterface
{
    /**
     * @param TableSection $node
     *
     * {@inheritdoc}
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer)
    {
        if (! $node instanceof TableSection) {
            throw new \InvalidArgumentException('Incompatible node type: ' . \get_class($node));
        }

        if (! $node->hasChildren()) {
            return '';
        }

        $attrs = $node->data->get('attributes');

        $separator = $childRenderer->getInnerSeparator();

        return new HtmlElement($node->getType(), $attrs, $separator . $childRenderer->renderNodes($node->children()) . $separator);
    }
}
