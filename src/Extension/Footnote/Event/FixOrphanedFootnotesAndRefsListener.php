<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\CommonMark\Extension\Footnote\Event;

use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\Footnote\Node\Footnote;
use League\CommonMark\Extension\Footnote\Node\FootnoteRef;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Reference\ReferenceableInterface;

final class FixOrphanedFootnotesAndRefsListener
{
    public function onDocumentParsed(DocumentParsedEvent $event): void
    {
        $document = $event->getDocument();
        $walker   = $document->walker();

        while ($event = $walker->next()) {
            if (! $event->isEntering()) {
                continue;
            }

            $node = $event->getNode();
            if ($node instanceof FootnoteRef && ! $this->exists($document, Footnote::class, $node->getReference()->getLabel())) {
                // Found an orphaned FootnoteRef without a corresponding Footnote
                // Restore the original footnote ref text
                $node->replaceWith(new Text(\sprintf('[^%s]', $node->getReference()->getLabel())));
            }

            // phpcs:disable SlevomatCodingStandard.ControlStructures.EarlyExit.EarlyExitNotUsed
            if ($node instanceof Footnote && ! $this->exists($document, FootnoteRef::class, $node->getReference()->getLabel())) {
                // Found an orphaned Footnote without a corresponding FootnoteRef
                // Remove the footnote
                $walker->resumeAt($node->next() ?? $node->parent());
                $node->detach();
            }
        }
    }

    private function exists(Document $document, string $type, string $label): bool
    {
        $walker = $document->walker();
        while ($event = $walker->next()) {
            if (! $event->isEntering()) {
                continue;
            }

            $node = $event->getNode();
            if (! ($node instanceof ReferenceableInterface && \get_class($node) === $type)) {
                continue;
            }

            if ($node->getReference()->getLabel() === $label) {
                return true;
            }
        }

        return false;
    }
}
