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

namespace League\CommonMark\Extension\HeadingPermalink;

use League\CommonMark\Configuration\ConfigurationAwareInterface;
use League\CommonMark\Configuration\ConfigurationInterface;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

/**
 * Renders the HeadingPermalink elements
 */
final class HeadingPermalinkRenderer implements NodeRendererInterface, ConfigurationAwareInterface
{
    public const DEFAULT_SYMBOL = '¶';

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
     * @param HeadingPermalink $node
     *
     * {@inheritdoc}
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer)
    {
        if (! $node instanceof HeadingPermalink) {
            throw new \InvalidArgumentException('Incompatible node type: ' . \get_class($node));
        }

        $slug = $node->getSlug();

        $idPrefix = (string) $this->config->get('heading_permalink/id_prefix', 'user-content');
        if ($idPrefix !== '') {
            $idPrefix .= '-';
        }

        $attrs = $node->data->getData('attributes');
        $attrs->set('id', $idPrefix . $slug);
        $attrs->set('href', '#' . $slug);
        $attrs->set('name', $slug);
        $attrs->append('class', $this->config->get('heading_permalink/html_class', 'heading-permalink'));
        $attrs->set('aria-hidden', 'true');
        $attrs->set('title', $this->config->get('heading_permalink/title', 'Permalink'));

        $symbol = $this->config->get('heading_permalink/symbol', self::DEFAULT_SYMBOL);
        \assert(\is_string($symbol));

        return new HtmlElement('a', $attrs->export(), \htmlspecialchars($symbol), false);
    }
}
