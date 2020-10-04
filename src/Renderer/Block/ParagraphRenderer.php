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

namespace League\CommonMark\Renderer\Block;

use League\CommonMark\Extension\CommonMark\Node\Block\ListBlock;
use League\CommonMark\Extension\CommonMark\Node\Block\ListItem;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

final class ParagraphRenderer implements NodeRendererInterface
{
    /**
     * @param Paragraph $node
     *
     * {@inheritdoc}
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer)
    {
        if (! ($node instanceof Paragraph)) {
            throw new \InvalidArgumentException('Incompatible node type: ' . \get_class($node));
        }

        if ($this->inTightList($node)) {
            return $childRenderer->renderNodes($node->children());
        }

        $attrs = $node->data->get('attributes');

        return new HtmlElement('p', $attrs, $childRenderer->renderNodes($node->children()));
    }

    private function inTightList(Paragraph $node): bool
    {
        $parent = $node->parent();
        if (! $parent instanceof ListItem) {
            return false;
        }

        $gramps = $parent->parent();
        if (! $gramps instanceof ListBlock) {
            return false;
        }

        return $gramps->isTight();
    }
}
