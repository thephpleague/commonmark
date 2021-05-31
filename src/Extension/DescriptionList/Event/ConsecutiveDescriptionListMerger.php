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

namespace League\CommonMark\Extension\DescriptionList\Event;

use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\DescriptionList\Node\DescriptionList;

final class ConsecutiveDescriptionListMerger
{
    public function __invoke(DocumentParsedEvent $event): void
    {
        $walker = $event->getDocument()->walker();
        while ($e = $walker->next()) {
            // Wait until we're exiting a description list node
            if ($e->isEntering()) {
                continue;
            }

            $node = $e->getNode();
            if (! $node instanceof DescriptionList) {
                continue;
            }

            if (! ($next = $node->next()) instanceof DescriptionList) {
                continue;
            }

            // There's another description list next; merge it into the current one
            foreach ($next->children() as $child) {
                $node->appendChild($child);
            }

            $next->detach();

            $walker->resumeAt($node, false);
        }
    }
}
