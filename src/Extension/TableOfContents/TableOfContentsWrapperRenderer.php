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

namespace League\CommonMark\Extension\TableOfContents;

use League\CommonMark\Exception\InvalidArgumentException;
use League\CommonMark\Extension\TableOfContents\Node\TableOfContentsWrapper;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use League\CommonMark\Xml\XmlNodeRendererInterface;

final class TableOfContentsWrapperRenderer implements NodeRendererInterface, XmlNodeRendererInterface
{
    /**
     * {@inheritDoc}
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer)
    {
        TableOfContentsWrapper::assertInstanceOf($node);
        $children = $node->children();
        if (! \is_array($children)) {
            /** @psalm-suppress NoValue */
            $children = \iterator_to_array($children);
        }

        if (\count($children) !== 2) {
            throw new InvalidArgumentException(
                'TableOfContentsWrapper nodes should have 2 children, found ' . \count($children)
            );
        }

        $attrs = $node->data->get('attributes');

        return new HtmlElement(
            'div',
            $attrs,
            $childRenderer->renderNodes($children)
        );
    }

    public function getXmlTagName(Node $node): string
    {
        return 'table_of_contents_wrapper';
    }

    /**
     * @return array<string, scalar>
     */
    public function getXmlAttributes(Node $node): array
    {
        return [];
    }
}
