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
use League\CommonMark\Node\Inline\Text;

final class FixOrphanedFootnotesAndRefsListener
{
    public function onDocumentParsed(DocumentParsedEvent $event): void
    {
        $document = $event->getDocument();

        /** @var array<string, Footnote> $definitions First definition of each label */
        $definitions = [];
        /** @var array<int, list<FootnoteRef>> $refsIn References, by ID of the innermost enclosing footnote (0 for none) */
        $refsIn = [];
        /** @var list<Footnote> $footnotes */
        $footnotes = [];

        /** @var array<int, int> $outer ID of the footnote each footnote is defined in (0 for none) */
        $outer     = [];
        $enclosing = [0];
        $walker    = $document->walker();
        while ($walkerEvent = $walker->next()) {
            $node = $walkerEvent->getNode();
            if ($node instanceof Footnote) {
                if (! $walkerEvent->isEntering()) {
                    \array_pop($enclosing);
                    continue;
                }

                $outer[\spl_object_id($node)]                     = \end($enclosing);
                $enclosing[]                                      = \spl_object_id($node);
                $footnotes[]                                      = $node;
                $definitions[$node->getReference()->getLabel()] ??= $node;
            } elseif ($node instanceof FootnoteRef && $walkerEvent->isEntering()) {
                $refsIn[\end($enclosing)][] = $node;
            }
        }

        // A footnote is used when it is referenced from the body or from another used footnote
        $used  = [];
        $queue = [0];
        while (($id = \array_pop($queue)) !== null) {
            foreach ($refsIn[$id] ?? [] as $ref) {
                $label    = $ref->getReference()->getLabel();
                $footnote = $definitions[$label] ?? null;
                if ($footnote === null) {
                    $ref->replaceWith(new Text(\sprintf('[^%s]', $label)));
                    continue;
                }

                $footnoteId = \spl_object_id($footnote);
                if (isset($used[$footnoteId])) {
                    continue;
                }

                $used[$footnoteId] = true;
                $queue[]           = $footnoteId;
            }
        }

        foreach ($footnotes as $footnote) {
            if (! isset($used[\spl_object_id($footnote)])) {
                $footnote->detach();
            } elseif (($o = $outer[\spl_object_id($footnote)]) !== 0 && ! isset($used[$o])) {
                // Keep a used footnote that was defined inside an unused one
                $document->appendChild($footnote);
            }
        }
    }
}
