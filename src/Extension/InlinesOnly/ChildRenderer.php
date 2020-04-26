<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\InlinesOnly;

use League\CommonMark\Configuration\ConfigurationAwareInterface;
use League\CommonMark\Configuration\ConfigurationInterface;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Inline\AbstractInline;
use League\CommonMark\Renderer\Block\BlockRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;

/**
 * Simply renders child elements as-is, adding newlines as needed.
 */
final class ChildRenderer implements BlockRendererInterface, ConfigurationAwareInterface
{
    /** @var ConfigurationInterface */
    private $config;

    public function render(AbstractBlock $block, NodeRendererInterface $htmlRenderer, bool $inTightList = false)
    {
        $out = '';
        $lastItemWasBlock = false;

        foreach ($block->children() as $child) {
            if ($lastItemWasBlock) {
                $lastItemWasBlock = false;
                $out .= $this->config->get('renderer/block_separator', "\n");
            }

            if ($child instanceof AbstractBlock) {
                $out .= $htmlRenderer->renderBlock($child, $inTightList);
                $lastItemWasBlock = true;
            } elseif ($child instanceof AbstractInline) {
                $out .= $htmlRenderer->renderInline($child);
            }
        }

        if (!$block instanceof Document) {
            $out .= $this->config->get('renderer/block_separator', "\n");
        }

        return $out;
    }

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->config = $configuration;
    }
}
