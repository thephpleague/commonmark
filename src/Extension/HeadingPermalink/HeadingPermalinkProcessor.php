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
use League\CommonMark\Exception\InvalidOptionException;
use League\CommonMark\Extension\HeadingPermalink\Slug\SlugGeneratorInterface as DeprecatedSlugGeneratorInterface;
use League\CommonMark\Extension\HeadingPermalink\SlugGenerator\DefaultSlugGenerator;
use League\CommonMark\Extension\HeadingPermalink\SlugGenerator\SlugGeneratorInterface;
use League\CommonMark\Inline\Element\Code;
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

    /** @var SlugGeneratorInterface|DeprecatedSlugGeneratorInterface */
    private $slugGenerator;

    /** @var ConfigurationInterface */
    private $config;

    /**
     * @param SlugGeneratorInterface|DeprecatedSlugGeneratorInterface|null $slugGenerator
     */
    public function __construct($slugGenerator = null)
    {
        if ($slugGenerator instanceof DeprecatedSlugGeneratorInterface) {
            @trigger_error(sprintf('Passing a %s into the %s constructor is deprecated; use a %s instead', DeprecatedSlugGeneratorInterface::class, self::class, SlugGeneratorInterface::class), E_USER_DEPRECATED);
        }

        $this->slugGenerator = $slugGenerator ?? new DefaultSlugGenerator();
    }

    public function setConfiguration(ConfigurationInterface $configuration)
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

        if (!($generator instanceof DeprecatedSlugGeneratorInterface || $generator instanceof SlugGeneratorInterface)) {
            throw new InvalidOptionException('The heading_permalink/slug_generator option must be an instance of ' . SlugGeneratorInterface::class);
        }

        $this->slugGenerator = $generator;
    }

    private function addHeadingLink(Heading $heading): void
    {
        if ($this->slugGenerator instanceof DeprecatedSlugGeneratorInterface) {
            $text = $this->getChildText($heading);
            $slug = $this->slugGenerator->createSlug($text);
        } else {
            $slug = $this->slugGenerator->generateSlug($heading);
        }

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

    /**
     * @deprecated Not needed in 2.0
     */
    private function getChildText(Node $node): string
    {
        $text = '';

        $walker = $node->walker();
        while ($event = $walker->next()) {
            if ($event->isEntering() && (($child = $event->getNode()) instanceof Text || $child instanceof Code)) {
                $text .= $child->getContent();
            }
        }

        return $text;
    }
}
