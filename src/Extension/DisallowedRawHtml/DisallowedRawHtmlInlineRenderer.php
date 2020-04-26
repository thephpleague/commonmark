<?php

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
use League\CommonMark\Node\Inline\AbstractInline;
use League\CommonMark\Renderer\Inline\InlineRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;

final class DisallowedRawHtmlInlineRenderer implements InlineRendererInterface, ConfigurationAwareInterface
{
    /** @var InlineRendererInterface */
    private $htmlInlineRenderer;

    public function __construct(InlineRendererInterface $htmlBlockRenderer)
    {
        $this->htmlInlineRenderer = $htmlBlockRenderer;
    }

    public function render(AbstractInline $inline, NodeRendererInterface $htmlRenderer)
    {
        $rendered = $this->htmlInlineRenderer->render($inline, $htmlRenderer);

        if ($rendered === '') {
            return '';
        }

        // Match these types of tags: <title> </title> <title x="sdf"> <title/> <title />
        return preg_replace('/<(\/?(?:title|textarea|style|xmp|iframe|noembed|noframes|script|plaintext)[ \/>])/i', '&lt;$1', $rendered);
    }

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        if ($this->htmlInlineRenderer instanceof ConfigurationAwareInterface) {
            $this->htmlInlineRenderer->setConfiguration($configuration);
        }
    }
}
