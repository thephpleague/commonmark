<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on stmd.js
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Inline\Element;

class AbstractInlineContainer extends AbstractBaseInline
{
    /**
     * @var AbstractBaseInline[]
     */
    protected $inlineContents = array();

    /**
     * @param AbstractBaseInline[] $contents
     */
    public function __construct(array $contents = array())
    {
        $this->inlineContents = $contents;
    }

    /**
     * @return AbstractBaseInline[]
     */
    public function getInlineContents()
    {
        return $this->inlineContents;
    }

    /**
     * @param AbstractBaseInline[] $contents
     *
     * @return $this
     */
    public function setInlineContents($contents)
    {
        $this->inlineContents = $contents;

        return $this;
    }
}
