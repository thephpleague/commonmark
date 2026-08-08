<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 * (c) 2015 Martin Hasoň <martin.hason@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\CommonMark\Extension\Attributes\Event;

use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\Attributes\Node\Attributes;
use League\CommonMark\Extension\Attributes\Node\AttributesInline;
use League\CommonMark\Extension\Attributes\Util\AttributesHelper;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\ListBlock;
use League\CommonMark\Extension\CommonMark\Node\Block\ListItem;
use League\CommonMark\Node\Inline\AbstractInline;
use League\CommonMark\Node\Node;

final class AttributesListener
{
    private const DIRECTION_PREFIX = 'prefix';
    private const DIRECTION_SUFFIX = 'suffix';

    /** @var list<string> */
    private array $allowList;
    private bool $allowUnsafeLinks;

    /**
     * @param list<string> $allowList
     */
    public function __construct(array $allowList = [], bool $allowUnsafeLinks = true)
    {
        $this->allowList        = $allowList;
        $this->allowUnsafeLinks = $allowUnsafeLinks;
    }

    public function processDocument(DocumentParsedEvent $event): void
    {
        // Targets already worked out for attribute blocks we walked past; see findTargetAndDirection()
        /** @var \SplObjectStorage<Attributes|AttributesInline, array<Node|string|null>> $resolved */
        $resolved = new \SplObjectStorage();

        /**
         * Attributes waiting to be written to their target, keyed by target object ID.
         *
         * Everything is merged and filtered per node exactly as it would be if it were written
         * straight to the target, except for the class list: only its first (whole) value takes
         * part in the merges, with the classes contributed by later nodes recorded separately and
         * joined once at the end. Feeding the growing list back through mergeAttributes() for
         * every node would re-split and re-join all of it each time, which is quadratic.
         *
         * @var array<int, array{node: Node, attributes: array<string, mixed>, class: string|null, before: list<string>, after: list<string>}> $pending
         */
        $pending = [];

        foreach ($event->getDocument()->iterator() as $node) {
            if (! ($node instanceof Attributes || $node instanceof AttributesInline)) {
                continue;
            }

            [$target, $direction] = self::findTargetAndDirection($node, $resolved);

            if ($target instanceof Node) {
                $parent = $target->parent();
                if ($parent instanceof ListItem && $parent->parent() instanceof ListBlock && $parent->parent()->isTight()) {
                    $target = $parent;
                }

                $id = \spl_object_id($target);
                if (! isset($pending[$id])) {
                    /** @var array<string, mixed> $existing */
                    $existing     = (array) $target->data->get('attributes');
                    $pending[$id] = ['node' => $target, 'attributes' => $existing, 'class' => null, 'before' => [], 'after' => []];
                }

                $attributes = $node->getAttributes();
                if ($direction === self::DIRECTION_SUFFIX) {
                    $merged = AttributesHelper::mergeAttributes($pending[$id]['attributes'], $attributes);
                } else {
                    $merged = AttributesHelper::mergeAttributes($attributes, $pending[$id]['attributes']);
                }

                $merged = AttributesHelper::filterAttributes($merged, $this->allowList, $this->allowUnsafeLinks);

                if (! isset($merged['class'])) {
                    $pending[$id]['class']  = null;
                    $pending[$id]['before'] = $pending[$id]['after'] = [];
                } elseif ($pending[$id]['class'] === null) {
                    $pending[$id]['class'] = $merged['class'];
                } else {
                    // Only this node's own classes can have been added to the list, so record them
                    // and put the short value the merge started from back in their place.
                    $added = AttributesHelper::classList($attributes['class'] ?? []);
                    if ($direction === self::DIRECTION_SUFFIX) {
                        foreach ($added as $class) {
                            $pending[$id]['after'][] = $class;
                        }
                    } else {
                        foreach (\array_reverse($added) as $class) {
                            $pending[$id]['before'][] = $class;
                        }
                    }

                    $merged['class'] = $pending[$id]['class'];
                }

                $pending[$id]['attributes'] = $merged;
            }

            $node->detach();
        }

        foreach ($pending as $entry) {
            $attributes = $entry['attributes'];
            if ($entry['class'] !== null && ($entry['before'] !== [] || $entry['after'] !== [])) {
                $classes   = \array_reverse($entry['before']);
                $classes[] = $entry['class'];

                $attributes['class'] = \implode(' ', \array_merge($classes, $entry['after']));
            }

            $entry['node']->data->set('attributes', $attributes);
        }
    }

    /**
     * @param Attributes|AttributesInline                                             $node
     * @param \SplObjectStorage<Attributes|AttributesInline, array<Node|string|null>> $resolved
     *
     * @return array<Node|string|null>
     */
    private static function findTargetAndDirection($node, \SplObjectStorage $resolved): array
    {
        if (isset($resolved[$node])) {
            $result = $resolved[$node];
            // Each node is only ever resolved once, so don't keep it alive any longer
            unset($resolved[$node]);

            return $result;
        }

        $target    = null;
        $direction = null;
        $previous  = $next = $node;
        /** @var list<Attributes> $shared */
        $shared = [];
        while (true) {
            $previous = self::getPrevious($previous);
            $next     = self::getNext($next);

            if ($previous === null && $next === null) {
                if (! $node->parent() instanceof FencedCode) {
                    $target    = $node->parent();
                    $direction = self::DIRECTION_SUFFIX;
                }

                break;
            }

            if ($node instanceof AttributesInline && ($previous === null || ($previous instanceof AbstractInline && $node->isBlock()))) {
                // Once this condition holds it holds for every remaining iteration, as walking
                // further to the left can only ever yield another inline sibling or null. No
                // sibling can therefore be chosen, so the target must be the parent; continuing
                // to walk would only re-scan the remaining siblings for nothing.
                if (! $node->parent() instanceof FencedCode) {
                    $target    = $node->parent();
                    $direction = self::DIRECTION_SUFFIX;
                }

                break;
            }

            if ($previous !== null && ! self::isAttributesNode($previous)) {
                $target    = $previous;
                $direction = self::DIRECTION_SUFFIX;

                break;
            }

            if ($next !== null && ! self::isAttributesNode($next)) {
                $target    = $next;
                $direction = self::DIRECTION_PREFIX;

                break;
            }

            // Only a TARGET_NEXT block can have a sibling block to walk past, so if that's what
            // we're looking at then we are one too, which means getPrevious() already returned
            // null for us and will keep doing so. Neither of us can therefore pick anything up on
            // the left, and what is left of this walk is the whole of that block's own walk: it
            // must end up with the same target we do. Remember that instead of re-scanning the
            // chain once per block.
            if (! ($next instanceof Attributes) || $next->getTarget() !== Attributes::TARGET_NEXT) {
                continue;
            }

            $shared[] = $next;
        }

        /** @var array<Node|string|null> $result */
        $result = [$target, $direction];
        foreach ($shared as $sharedNode) {
            $resolved[$sharedNode] = $result;
        }

        return $result;
    }

    /**
     * Get any previous block (sibling or parent) this might apply to
     */
    private static function getPrevious(?Node $node = null): ?Node
    {
        if ($node instanceof Attributes) {
            if ($node->getTarget() === Attributes::TARGET_NEXT) {
                return null;
            }

            if ($node->getTarget() === Attributes::TARGET_PARENT) {
                return $node->parent();
            }
        }

        return $node instanceof Node ? $node->previous() : null;
    }

    /**
     * Get any previous block (sibling or parent) this might apply to
     */
    private static function getNext(?Node $node = null): ?Node
    {
        if ($node instanceof Attributes && $node->getTarget() !== Attributes::TARGET_NEXT) {
            return null;
        }

        return $node instanceof Node ? $node->next() : null;
    }

    private static function isAttributesNode(Node $node): bool
    {
        return $node instanceof Attributes || $node instanceof AttributesInline;
    }
}
