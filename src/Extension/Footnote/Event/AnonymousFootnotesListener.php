<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 * (c) Rezo Zero / Ambroise Maupate
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\CommonMark\Extension\Footnote\Event;

use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\Footnote\Node\Footnote;
use League\CommonMark\Extension\Footnote\Node\FootnoteBackref;
use League\CommonMark\Extension\Footnote\Node\FootnoteRef;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Normalizer\TextNormalizer;
use League\CommonMark\Reference\Reference;
use League\Config\ConfigurationAwareInterface;
use League\Config\ConfigurationInterface;

final class AnonymousFootnotesListener implements ConfigurationAwareInterface
{
    private ConfigurationInterface $config;

    private TextNormalizer $normalizer;

    public function __construct()
    {
        $this->normalizer = new TextNormalizer();
    }

    public function onDocumentParsed(DocumentParsedEvent $event): void
    {
        $document = $event->getDocument();

        // Labels of anonymous footnotes come from their text, so they must be kept apart from explicit labels.
        // Labels are compared like ids and reference map keys, so ones that differ only in case are the same.
        /** @var array<string, int> $takenLabels Keyed by normalized label */
        $takenLabels = [];
        /** @var list<FootnoteRef> $anonymousRefs */
        $anonymousRefs = [];
        foreach ($document->iterator() as $node) {
            if ($node instanceof FootnoteRef && $node->getContent() !== null) {
                $anonymousRefs[] = $node;
            } elseif ($node instanceof FootnoteRef || $node instanceof Footnote) {
                $takenLabels[$this->normalizer->normalize($node->getReference()->getLabel())] = 1;
            }
        }

        foreach ($anonymousRefs as $node) {
            $text = $node->getContent();
            \assert($text !== null);

            $existingReference = $node->getReference();
            $label             = $this->claimLabel($existingReference->getLabel(), $takenLabels);
            if ($label !== $existingReference->getLabel()) {
                $node->setReference(new Reference(
                    $label,
                    '#' . $this->config->get('footnote/footnote_id_prefix') . $label,
                    $existingReference->getTitle()
                ));
            }

            // Anonymous footnote needs to create a footnote from its content
            $newReference = new Reference(
                $label,
                '#' . $this->config->get('footnote/ref_id_prefix') . $label,
                $existingReference->getTitle()
            );

            $paragraph = new Paragraph();
            $paragraph->appendChild(new Text($text));
            $paragraph->appendChild(new FootnoteBackref($newReference));

            $footnote = new Footnote($newReference);
            $footnote->appendChild($paragraph);

            $document->appendChild($footnote);
        }
    }

    /**
     * @param array<string, int> $takenLabels Each taken label, mapped to the next suffix to try for it
     */
    private function claimLabel(string $label, array &$takenLabels): string
    {
        $key = $this->normalizer->normalize($label);
        if (isset($takenLabels[$key])) {
            $suffix = $takenLabels[$key];
            while (isset($takenLabels[$key . '-' . $suffix])) {
                $suffix++;
            }

            $takenLabels[$key] = $suffix + 1;
            $label            .= '-' . $suffix;
            $key              .= '-' . $suffix;
        }

        $takenLabels[$key] = 1;

        return $label;
    }

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->config = $configuration;
    }
}
