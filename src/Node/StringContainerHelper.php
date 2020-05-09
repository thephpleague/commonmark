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

namespace League\CommonMark\Node;

final class StringContainerHelper
{
    /**
     * Extract text literals from all descendant nodes
     *
     * @param Node          $node         Parent node
     * @param array<string> $excludeTypes Optional list of node class types to exclude
     *
     * @return string Concatenated literals
     */
    public static function getChildText(Node $node, array $excludeTypes = []): string
    {
        $text = '';

        $walker = $node->walker();
        while ($event = $walker->next()) {
            if ($event->isEntering() && ($child = $event->getNode()) instanceof StringContainerInterface && ! self::isOneOf($child, $excludeTypes)) {
                $text .= $child->getLiteral();
            }
        }

        return $text;
    }

    /**
     * @param string[] $classesOrInterfacesToCheck
     */
    private static function isOneOf(object $object, array $classesOrInterfacesToCheck): bool
    {
        foreach ($classesOrInterfacesToCheck as $type) {
            if ($object instanceof $type) {
                return true;
            }
        }

        return false;
    }
}
