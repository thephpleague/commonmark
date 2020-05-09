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

namespace League\CommonMark\Extension\DisallowedRawHtml;

use League\CommonMark\Configuration\ConfigurationAwareInterface;
use League\CommonMark\Configuration\ConfigurationInterface;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;

final class DisallowedRawHtmlRenderer implements NodeRendererInterface, ConfigurationAwareInterface
{
    /**
     * @var NodeRendererInterface
     *
     * @psalm-readonly
     */
    private $innerRenderer;

    public function __construct(NodeRendererInterface $innerRenderer)
    {
        $this->innerRenderer = $innerRenderer;
    }

    /**
     * {@inheritdoc}
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer)
    {
        $rendered = (string) $this->innerRenderer->render($node, $childRenderer);

        if ($rendered === '') {
            return '';
        }

        // Match these types of tags: <title> </title> <title x="sdf"> <title/> <title />
        return \preg_replace('/<(\/?(?:title|textarea|style|xmp|iframe|noembed|noframes|script|plaintext)[ \/>])/i', '&lt;$1', $rendered);
    }

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        if ($this->innerRenderer instanceof ConfigurationAwareInterface) {
            $this->innerRenderer->setConfiguration($configuration);
        }
    }
}
