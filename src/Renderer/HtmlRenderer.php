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

namespace League\CommonMark\Renderer;

use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Event\DocumentRenderedEvent;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Node;
use League\CommonMark\Output\RenderedContent;
use League\CommonMark\Output\RenderedContentInterface;
use League\CommonMark\Util\HtmlElement;

final class HtmlRenderer implements HtmlRendererInterface, ChildNodeRendererInterface
{
    /**
     * @var EnvironmentInterface
     *
     * @psalm-readonly
     */
    private $environment;

    public function __construct(EnvironmentInterface $environment)
    {
        $this->environment = $environment;
    }

    public function renderDocument(Document $node): RenderedContentInterface
    {
        $output = new RenderedContent($node, (string) $this->renderNode($node));

        $event = new DocumentRenderedEvent($output);
        $this->environment->dispatch($event);

        return $event->getOutput();
    }

    /**
     * {@inheritdoc}
     */
    public function renderNodes(iterable $nodes): string
    {
        $output = '';

        // Track whether the previous item was a block, as we'll need to insert newlines after them
        $lastItemWasBlock = false;

        foreach ($nodes as $node) {
            if ($lastItemWasBlock) {
                $lastItemWasBlock = false;
                $output          .= $this->getBlockSeparator();
            }

            $output .= $this->renderNode($node);

            if ($node instanceof AbstractBlock) {
                $lastItemWasBlock = true;
            }
        }

        return $output;
    }

    /**
     * @return HtmlElement|string
     *
     * @throws \RuntimeException
     */
    private function renderNode(Node $node)
    {
        $renderers = $this->environment->getRenderersForClass(\get_class($node));

        foreach ($renderers as $renderer) {
            \assert($renderer instanceof NodeRendererInterface);
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
