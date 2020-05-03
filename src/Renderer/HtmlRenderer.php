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

namespace League\CommonMark\Renderer;

use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Node;

final class HtmlRenderer implements HtmlRendererInterface, ChildNodeRendererInterface
{
    /**
     * @var EnvironmentInterface
     */
    private $environment;

    /**
     * @param EnvironmentInterface $environment
     */
    public function __construct(EnvironmentInterface $environment)
    {
        $this->environment = $environment;
    }

    public function renderDocument(Document $node): string
    {
        return $this->renderNode($node);
    }

    public function renderNodes(iterable $nodes): string
    {
        $out = '';
        $lastItemWasBlock = false;

        foreach ($nodes as $node) {
            if ($lastItemWasBlock) {
                $lastItemWasBlock = false;
                $out .= $this->getBlockSeparator();
            }

            $out .= $this->renderNode($node);

            if ($node instanceof AbstractBlock) {
                $lastItemWasBlock = true;
            }
        }

        return $out;
    }

    /**
     * @param Node $node
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    private function renderNode(Node $node): string
    {
        $renderers = $this->environment->getRenderersForClass(\get_class($node));

        /** @var NodeRendererInterface $renderer */
        foreach ($renderers as $renderer) {
            if (($result = $renderer->render($node, $this)) !== null) {
                return $result;
            }
        }

        throw new \RuntimeException('Unable to find corresponding renderer for node type ' . \get_class($node));
    }

    public function getBlockSeparator(): string
    {
        return $this->environment->getConfig('renderer/block_separator', "\n");
    }

    public function getInnerSeparator(): string
    {
        return $this->environment->getConfig('renderer/inner_separator', "\n");
    }
}
