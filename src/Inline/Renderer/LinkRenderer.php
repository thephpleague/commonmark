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

namespace League\CommonMark\Inline\Renderer;

use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Util\Configuration;
use League\CommonMark\Util\ConfigurationAwareInterface;
use League\CommonMark\Util\RegexHelper;
use League\CommonMark\Util\Xml;

class LinkRenderer implements InlineRendererInterface, ConfigurationAwareInterface
{
    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @param Link                     $inline
     * @param ElementRendererInterface $htmlRenderer
     *
     * @return HtmlElement
     */
    public function render(AbstractInline $inline, ElementRendererInterface $htmlRenderer)
    {
        if (!($inline instanceof Link)) {
            throw new \InvalidArgumentException('Incompatible inline type: ' . get_class($inline));
        }

        $attrs = [];
        foreach ($inline->getData('attributes', []) as $key => $value) {
            $attrs[$key] = Xml::escape($value, true);
        }

        $forbidUnsafeLinks = $this->config->getConfig('safe') || !$this->config->getConfig('allow_unsafe_links');
        if (!($forbidUnsafeLinks && RegexHelper::isLinkPotentiallyUnsafe($inline->getUrl()))) {
            $attrs['href'] = Xml::escape($inline->getUrl(), true);
        }

        if (isset($inline->data['title'])) {
            $attrs['title'] = Xml::escape($inline->data['title'], true);
        }

        if (isset($attrs['target']) && $attrs['target'] === '_blank' && !isset($attrs['rel'])) {
            $attrs['rel'] = 'noopener noreferrer';
        }

        return new HtmlElement('a', $attrs, $htmlRenderer->renderInlines($inline->children()));
    }

    /**
     * @param Configuration $configuration
     */
    public function setConfiguration(Configuration $configuration)
    {
        $this->config = $configuration;
    }
}
