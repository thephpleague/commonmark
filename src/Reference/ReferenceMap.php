<?php

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
    /**
     * @var ReferenceInterface[]
     */
    protected $references = [];

    /**
     * {@inheritdoc}
     */
    public function addReference(ReferenceInterface $reference): void
    {
        $key = Reference::normalizeReference($reference->getLabel());
        $this->references[$key] = $reference;
    }

    /**
     * {@inheritdoc}
     */
    public function contains(string $label): bool
    {
        $label = Reference::normalizeReference($label);

        return isset($this->references[$label]);
    }

    /**
     * {@inheritdoc}
     */
    public function getReference(string $label): ?ReferenceInterface
    {
        $label = Reference::normalizeReference($label);

        if (!isset($this->references[$label])) {
            return null;
        }

        return $this->references[$label];
    }

    /**
     * {@inheritdoc}
     */
    public function listReferences(): iterable
    {
        return \array_values($this->references);
    }
}
