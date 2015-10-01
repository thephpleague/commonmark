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

namespace League\CommonMark;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Delimiter\DelimiterStack;
use League\CommonMark\Reference\ReferenceMap;

class InlineParserContext
{
    private $container;
    private $referenceMap;
    private $cursor;
    private $delimiterStack;

    public function __construct(AbstractBlock $container, ReferenceMap $referenceMap)
    {
        $this->referenceMap = $referenceMap;
        $this->container = $container;
        $this->cursor = new Cursor(trim($container->getStringContent()));
        $this->delimiterStack = new DelimiterStack();
    }

    /**
     * @return AbstractBlock
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return ReferenceMap
     */
    public function getReferenceMap()
    {
        return $this->referenceMap;
    }

    /**
     * @return Cursor
     */
    public function getCursor()
    {
        return $this->cursor;
    }

    /**
     * @return DelimiterStack
     */
    public function getDelimiterStack()
    {
        return $this->delimiterStack;
    }
}
