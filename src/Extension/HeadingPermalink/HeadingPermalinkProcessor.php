<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\HeadingPermalink;

use League\CommonMark\Block\Element\Heading;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\HeadingPermalink\Slug\DefaultSlugGenerator;
use League\CommonMark\Extension\HeadingPermalink\Slug\SlugGeneratorInterface;
use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Node\Node;
use League\CommonMark\Util\ConfigurationAwareInterface;
use League\CommonMark\Util\ConfigurationInterface;

/**
 * Searches the Document for Heading elements and adds HeadingPermalinks to each one
 */
final class HeadingPermalinkProcessor implements ConfigurationAwareInterface
{
    const INSERT_BEFORE = 'before';
    const INSERT_AFTER = 'after';

    /** @var SlugGeneratorInterface */
    private $slugGenerator;

    /** @var ConfigurationInterface */
    private $config;

    public function __construct(SlugGeneratorInterface $slugGenerator = null)
    {
        $this->slugGenerator = $slugGenerator ?? new DefaultSlugGenerator();
    }

    public function setConfiguration(ConfigurationInterface $configuration)
    {
        $this->config = $configuration;
    }

    public function __invoke(DocumentParsedEvent $e): void
    {
        $walker = $e->getDocument()->walker();

        while ($event = $walker->next()) {
            $node = $event->getNode();
            if ($node instanceof Heading && $event->isEntering()) {
                $this->addHeadingLink($node);
            }
        }
    }

    private function addHeadingLink(Heading $heading): void
    {
        $text = $this->getChildText($heading);
        $slug = $this->slugGenerator->createSlug($text);

        $headingLinkAnchor = new HeadingPermalink($slug);

        switch ($this->config->get('heading_permalink/insert', 'before')) {
            case self::INSERT_BEFORE:
                $heading->prependChild($headingLinkAnchor);

                return;
            case self::INSERT_AFTER:
                $heading->appendChild($headingLinkAnchor);

                return;
            default:
                throw new \RuntimeException("Invalid configuration value for heading_permalink/insert; expected 'before' or 'after'");
        }
    }

    private function getChildText(Node $node): string
    {
        $text = '';

        $walker = $node->walker();
        while ($event = $walker->next()) {
            if ($event->isEntering() && ($child = $event->getNode()) instanceof Text) {
                $text .= $child->getContent();
            }
        }

        return $text;
    }
}
