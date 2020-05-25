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
use League\CommonMark\Extension\Footnote\Node\FootnoteContainer;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Reference\Reference;

final class GatherFootnotesListener
{
    public function onDocumentParsed(DocumentParsedEvent $event): void
    {
        $document = $event->getDocument();
        $walker   = $document->walker();

        $footnotes = [];
        while ($event = $walker->next()) {
            if (! $event->isEntering()) {
                continue;
            }

            $node = $event->getNode();
            if (! $node instanceof Footnote) {
                continue;
            }

            // Look for existing reference with footnote label
            $ref = $document->getReferenceMap()->get($node->getReference()->getLabel());
            if ($ref !== null) {
                // Use numeric title to get footnotes order
                $footnotes[\intval($ref->getTitle())] = $node;
            } else {
                // Footnote call is missing, append footnote at the end
                $footnotes[\PHP_INT_MAX] = $node;
            }

            $backrefs = $document->getData('#fn:' . $node->getReference()->getDestination(), []);
            $this->createBackrefs($node, $backrefs);
        }

        // Only add a footnote container if there are any
        if (\count($footnotes) === 0) {
            return;
        }

        $container = $this->getFootnotesContainer($document);

        \ksort($footnotes);
        foreach ($footnotes as $footnote) {
            $container->appendChild($footnote);
        }
    }

    private function getFootnotesContainer(Document $document): FootnoteContainer
    {
        $footnoteContainer = new FootnoteContainer();
        $document->appendChild($footnoteContainer);

        return $footnoteContainer;
    }

    /**
     * Look for all footnote refs pointing to this footnote and create each footnote backrefs.
     *
     * @param Footnote    $node     The target footnote
     * @param Reference[] $backrefs References to create backrefs for
     */
    private function createBackrefs(Footnote $node, array $backrefs): void
    {
        // Backrefs should be added to the child paragraph
        $target = $node->lastChild();
        if ($target === null) {
            // This should never happen, but you never know
            $target = $node;
        }

        foreach ($backrefs as $backref) {
            $target->appendChild(new FootnoteBackref(new Reference(
                $backref->getLabel(),
                '#fnref:' . $backref->getLabel(),
                $backref->getTitle()
            )));
        }
    }
}
