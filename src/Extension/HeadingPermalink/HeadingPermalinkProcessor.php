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

use League\CommonMark\Configuration\ConfigurationInterface;
use League\CommonMark\Environment\EnvironmentAwareInterface;
use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\StringContainerHelper;
use League\CommonMark\Normalizer\TextNormalizerInterface;

/**
 * Searches the Document for Heading elements and adds HeadingPermalinks to each one
 */
final class HeadingPermalinkProcessor implements EnvironmentAwareInterface
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

    public function setEnvironment(EnvironmentInterface $environment): void
    {
        $this->config         = $environment->getConfiguration();
        $this->slugNormalizer = $environment->getSlugNormalizer();
    }

    public function __invoke(DocumentParsedEvent $e): void
    {
        $min = (int) $this->config->get('heading_permalink/min_heading_level');
        $max = (int) $this->config->get('heading_permalink/max_heading_level');

        $slugLength = (int) $this->config->get('heading_permalink/slug_length');

        $walker = $e->getDocument()->walker();

        while ($event = $walker->next()) {
            $node = $event->getNode();
            if ($node instanceof Heading && $event->isEntering() && $node->getLevel() >= $min && $node->getLevel() <= $max) {
                $this->addHeadingLink($node, $e->getDocument(), $slugLength);
            }
        }
    }

    private function addHeadingLink(Heading $heading, Document $document, int $slugLength): void
    {
        $text = StringContainerHelper::getChildText($heading);
        $slug = $this->slugNormalizer->normalize($text, [
            'node' => $heading,
            'length' => $slugLength,
        ]);

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
}
