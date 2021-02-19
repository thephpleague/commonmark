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
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\StringContainerHelper;
use League\CommonMark\Normalizer\TextNormalizerInterface;

/**
 * Searches the Document for Heading elements and adds HeadingPermalinks to each one
 */
final class HeadingPermalinkProcessor implements ConfigurationAwareInterface
{
    public const INSERT_BEFORE = 'before';
    public const INSERT_AFTER  = 'after';

    /**
     * @var TextNormalizerInterface
     *
     * @psalm-readonly-allow-private-mutation
     */
    private $slugNormalizer;

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

    public function __invoke(DocumentParsedEvent $e): void
    {
        $this->slugNormalizer = $this->config->get('heading_permalink/slug_normalizer');

        $min = (int) $this->config->get('heading_permalink/min_heading_level');
        $max = (int) $this->config->get('heading_permalink/max_heading_level');

        $e->getDocument()->data->set('heading_ids', []);

        $walker = $e->getDocument()->walker();

        while ($event = $walker->next()) {
            $node = $event->getNode();
            if ($node instanceof Heading && $event->isEntering() && $node->getLevel() >= $min && $node->getLevel() <= $max) {
                $this->addHeadingLink($node, $e->getDocument());
            }
        }
    }

    private function addHeadingLink(Heading $heading, Document $document): void
    {
        $text = StringContainerHelper::getChildText($heading);
        $slug = $this->slugNormalizer->normalize($text, $heading);

        $slug = $this->ensureUnique($slug, $document);

        $headingLinkAnchor = new HeadingPermalink($slug);

        switch ($this->config->get('heading_permalink/insert')) {
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

    private function ensureUnique(string $proposed, Document $document): string
    {
        $usedIds = $document->data->get('heading_ids');

        // Quick path, it's a unique ID
        if (! isset($usedIds[$proposed])) {
            $usedIds[$proposed] = true;
            $document->data->set('heading_ids', $usedIds);

            return $proposed;
        }

        $extension = 0;
        do {
            ++$extension;
            $id = \sprintf('%s-%s', $proposed, $extension);
        } while (isset($usedIds[$id]));

        $usedIds[$id] = true;
        $document->data->set('heading_ids', $usedIds);

        return $id;
    }
}
