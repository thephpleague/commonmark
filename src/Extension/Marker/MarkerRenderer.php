<?php

declare(strict_types=1);

namespace League\CommonMark\Extension\Marker;

use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use League\CommonMark\Xml\XmlNodeRendererInterface;

final class MarkerRenderer implements NodeRendererInterface, XmlNodeRendererInterface
{
    /**
     * @param Marker $node
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): \Stringable
    {
        Marker::assertInstanceOf($node);

        return new HtmlElement('mark', $node->data->get('attributes'), $childRenderer->renderNodes($node->children()));
    }

    public function getXmlTagName(Node $node): string
    {
        return 'mark';
    }

    public function getXmlAttributes(Node $node): array
    {
        return [];
    }
}