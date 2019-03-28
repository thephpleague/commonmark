<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Inline;

use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Node\Node;

/**
 * @internal
 */
final class AdjoiningTextCollapser
{
    /**
     * @param Node $container
     *
     * @internal
     */
    public static function collapseTextNodes(Node $container)
    {
        $walker = $container->walker();
        while (($event = $walker->next()) !== null) {
            if ($event->isEntering()) {
                $node = $event->getNode();
                $next = $node->next();
                if ($node instanceof Text && $next instanceof Text) {
                    $node->append($next->getContent());
                    $next->detach();
                    // Re-start the next `while` iteration at the same spot as before
                    $walker->resumeAt($node, true);
                }
            }
        }
    }
}
