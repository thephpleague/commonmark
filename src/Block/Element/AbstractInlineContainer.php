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

namespace League\CommonMark\Block\Element;

use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Util\ArrayCollection;

abstract class AbstractInlineContainer extends AbstractBlock
{
    /**
     * @var AbstractInline
     */
    protected $firstInline;

    /**
     * @var AbstractInline
     */
    protected $lastInline;

    /**
     * @return ArrayCollection|AbstractInline[]
     */
    public function getInlines()
    {
        $inlines = [];
        for($current = $this->firstInline;$current;$current = $current->next) {
            $inlines[] = $current;
        }

        return $inlines;
    }

    /**
     * @param ArrayCollection|AbstractInline[] $inlines
     *
     * @return $this
     */
    public function setInlines($inlines)
    {
        if (!is_array($inlines) && !(is_object($inlines) && $inlines instanceof ArrayCollection)) {
            throw new \InvalidArgumentException(sprintf('Expect iterable, got %s', get_class($inlines)));
        }

        foreach ($this->getInlines() as $inline) {
            $inline->unlink();
        }

        foreach ($inlines as $inline) {
            $inline->parent = $this;
            if (!$this->lastInline) {
                $this->firstInline = $this->lastInline = $inline;
            } else {
                $inline->previous = $this->lastInline;
                $this->lastInline->next = $inline;

                if (!$inline->next) {
                    $this->lastInline = $inline;
                }
            }
        }

        return $this;
    }
}
