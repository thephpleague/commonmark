<?php

/*
 * This file is part of the commonmark-php package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on stmd.js
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ColinODell\CommonMark\Reference;

/**
 * A collection of references, indexed by label
 */
class ReferenceMap
{
    /**
     * @var Reference[]
     */
    protected $references = array();

    /**
     * @param Reference $reference
     *
     * @return $this
     */
    public function addReference(Reference $reference)
    {
        $key = Reference::normalizeReference($reference->getLabel());
        $this->references[$key] = $reference;

        return $this;
    }

    /**
     * @param string $label
     *
     * @return bool
     */
    public function contains($label)
    {
        $label = Reference::normalizeReference($label);

        return isset($this->references[$label]);
    }

    /**
     * @param string $label
     *
     * @return Reference|null
     */
    public function getReference($label)
    {
        $label = Reference::normalizeReference($label);

        if (isset($this->references[$label])) {
            return $this->references[$label];
        } else {
            return null;
        }
    }
}
