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
use League\CommonMark\Exception\InvalidOptionException;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\HeadingPermalink\SlugGenerator\DefaultSlugGenerator;
use League\CommonMark\Extension\HeadingPermalink\SlugGenerator\SlugGeneratorInterface;

/**
 * Searches the Document for Heading elements and adds HeadingPermalinks to each one
 */
final class HeadingPermalinkProcessor implements ConfigurationAwareInterface
{
    public const INSERT_BEFORE = 'before';
    public const INSERT_AFTER  = 'after';

    /**
     * @var SlugGeneratorInterface
     *
     * @psalm-readonly-allow-private-mutation
     */
    private $slugGenerator;

    /**
     * @var ConfigurationInterface
     *
     * @psalm-readonly-allow-private-mutation
     */
    private $config;

    public function __construct(?SlugGeneratorInterface $slugGenerator = null)
    {
        $this->slugGenerator = $slugGenerator ?? new DefaultSlugGenerator();
    }

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->config = $configuration;
    }

    public function __invoke(DocumentParsedEvent $e): void
    {
        $this->useSlugGeneratorFromConfigurationIfProvided();

        $walker = $e->getDocument()->walker();

        while ($event = $walker->next()) {
            $node = $event->getNode();
            if ($node instanceof Heading && $event->isEntering()) {
                $this->addHeadingLink($node);
            }
        }
    }

    private function useSlugGeneratorFromConfigurationIfProvided(): void
    {
        $generator = $this->config->get('heading_permalink/slug_generator');
        if ($generator === null) {
            return;
        }

        if (! ($generator instanceof SlugGeneratorInterface)) {
            throw new InvalidOptionException('The heading_permalink/slug_generator option must be an instance of ' . SlugGeneratorInterface::class);
        }

        $this->slugGenerator = $generator;
    }

    private function addHeadingLink(Heading $heading): void
    {
        $slug = $this->slugGenerator->generateSlug($heading);

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
}
