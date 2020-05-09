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

namespace League\CommonMark\Renderer\Inline;

use League\CommonMark\Configuration\ConfigurationAwareInterface;
use League\CommonMark\Configuration\ConfigurationInterface;
use League\CommonMark\Node\Inline\Newline;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

final class NewlineRenderer implements NodeRendererInterface, ConfigurationAwareInterface
{
    /**
     * @var ConfigurationInterface
     *
     * @psalm-readonly-allow-private-mutation
     */
    private $config;

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->config = $configuration;
    }

    /**
     * @param Newline $node
     *
     * {@inheritdoc}
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer)
    {
        if (! ($node instanceof Newline)) {
            throw new \InvalidArgumentException('Incompatible node type: ' . \get_class($node));
        }

        if ($node->getType() === Newline::HARDBREAK) {
            return new HtmlElement('br', [], '', true) . "\n";
        }

        return $this->config->get('renderer/soft_break', "\n");
    }
}
