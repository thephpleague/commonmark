<?php

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
