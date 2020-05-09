<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Reference;

/**
 * A collection of references, indexed by label
 */
final class ReferenceMap implements ReferenceMapInterface
{
    /** @var array<string, ReferenceInterface> */
    private $references = [];

    public function add(ReferenceInterface $reference): void
    {
        // Normalize the key
        $key = Reference::normalizeReference($reference->getLabel());
        // Store the reference
        $this->references[$key] = $reference;
    }

    public function contains(string $label): bool
    {
        $label = Reference::normalizeReference($label);

        return isset($this->references[$label]);
    }

    public function get(string $label): ?ReferenceInterface
    {
        $label = Reference::normalizeReference($label);

        if (! isset($this->references[$label])) {
            return null;
        }

        return $this->references[$label];
    }

    /**
     * @return \Traversable<string, ReferenceInterface>
     */
    public function getIterator(): \Traversable
    {
        foreach ($this->references as $normalizedLabel => $reference) {
            yield $normalizedLabel => $reference;
        }
    }

    public function count(): int
    {
        return \count($this->references);
    }
}
