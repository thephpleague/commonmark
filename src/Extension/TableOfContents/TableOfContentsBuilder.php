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

namespace League\CommonMark\Extension\TableOfContents;

use League\CommonMark\Configuration\ConfigurationAwareInterface;
use League\CommonMark\Configuration\ConfigurationInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Exception\InvalidOptionException;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalink;
use League\CommonMark\Extension\TableOfContents\Node\TableOfContents;
use League\CommonMark\Extension\TableOfContents\Node\TableOfContentsPlaceholder;
use League\CommonMark\Node\Block\Document;

final class TableOfContentsBuilder implements ConfigurationAwareInterface
{
    public const POSITION_TOP             = 'top';
    public const POSITION_BEFORE_HEADINGS = 'before-headings';
    public const POSITION_PLACEHOLDER     = 'placeholder';

    /**
     * @var ConfigurationInterface
     *
     * @psalm-readonly-allow-private-mutation
     */
    private $config;

    public function onDocumentParsed(DocumentParsedEvent $event): void
    {
        $document = $event->getDocument();

        $generator = new TableOfContentsGenerator(
            (string) $this->config->get('table_of_contents/style', TableOfContentsGenerator::STYLE_BULLET),
            (string) $this->config->get('table_of_contents/normalize', TableOfContentsGenerator::NORMALIZE_RELATIVE),
            (int) $this->config->get('table_of_contents/min_heading_level', 1),
            (int) $this->config->get('table_of_contents/max_heading_level', 6)
        );

        $toc = $generator->generate($document);
        if ($toc === null) {
            // No linkable headers exist, so no TOC could be generated
            return;
        }

        // Add custom CSS class(es), if defined
        $class = $this->config->get('table_of_contents/html_class', 'table-of-contents');
        if ($class !== null) {
            $toc->data->append('attributes/class', $class);
        }

        // Add the TOC to the Document
        $position = $this->config->get('table_of_contents/position', self::POSITION_TOP);
        if ($position === self::POSITION_TOP) {
            $document->prependChild($toc);
        } elseif ($position === self::POSITION_BEFORE_HEADINGS) {
            $this->insertBeforeFirstLinkedHeading($document, $toc);
        } elseif ($position === self::POSITION_PLACEHOLDER) {
            $this->replacePlaceholders($document, $toc);
        } else {
            throw InvalidOptionException::forConfigOption('table_of_contents/position', $position);
        }
    }

    private function insertBeforeFirstLinkedHeading(Document $document, TableOfContents $toc): void
    {
        $walker = $document->walker();
        while ($event = $walker->next()) {
            if ($event->isEntering() && ($node = $event->getNode()) instanceof HeadingPermalink && ($parent = $node->parent()) instanceof Heading) {
                $parent->insertBefore($toc);

                return;
            }
        }
    }

    private function replacePlaceholders(Document $document, TableOfContents $toc): void
    {
        $walker = $document->walker();
        while ($event = $walker->next()) {
            // Add the block once we find a placeholder (and we're about to leave it)
            if (! $event->getNode() instanceof TableOfContentsPlaceholder) {
                continue;
            }

            if ($event->isEntering()) {
                continue;
            }

            $event->getNode()->replaceWith(clone $toc);
        }
    }

    public function setConfiguration(ConfigurationInterface $config): void
    {
        $this->config = $config;
    }
}
