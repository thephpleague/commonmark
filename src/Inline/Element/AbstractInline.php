<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Inline\Element;

use League\CommonMark\Block\Element\AbstractBlock;

abstract class AbstractInline
{
    /**
     * @var array
     *
     * Used for storage of arbitrary data
     */
    public $data = [];

    /**
     * @var AbstractInline|null
     */
    protected $parent;

    /**
     * @return AbstractBlock|AbstractInline|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param AbstractBlock|AbstractInline $parent
     *
     * @return $this
     */
    public function setParent($parent)
    {
        if (!$parent instanceof AbstractBlock && !$parent instanceof self) {
            throw new \InvalidArgumentException(sprintf(
                'Argument 1 passed to %s() must be an instance of %s or %s, instance of %s given',
                __METHOD__,
                'League\CommonMark\Block\Element\AbstractBlock',
                'League\CommonMark\Block\Element\AbstractInline',
                is_object($parent) ? get_class($parent) : gettype($parent)
            ));
        }

        $this->parent = $parent;

        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getData($key, $default = null)
    {
        return array_key_exists($key, $this->data) ? $this->data[$key] : $default;
    }
}
