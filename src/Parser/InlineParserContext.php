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

namespace League\CommonMark\Parser;

use League\CommonMark\Delimiter\DelimiterStack;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Reference\ReferenceMapInterface;

final class InlineParserContext
{
    /**
     * @var AbstractBlock
     *
     * @psalm-readonly
     */
    private $container;

    /**
     * @var ReferenceMapInterface
     *
     * @psalm-readonly
     */
    private $referenceMap;

    /**
     * @var Cursor
     *
     * @psalm-readonly
     */
    private $cursor;

    /**
     * @var DelimiterStack
     *
     * @psalm-readonly
     */
    private $delimiterStack;

    public function __construct(string $contents, AbstractBlock $container, ReferenceMapInterface $referenceMap)
    {
        $this->referenceMap   = $referenceMap;
        $this->container      = $container;
        $this->cursor         = new Cursor(\trim($contents));
        $this->delimiterStack = new DelimiterStack();
    }

    public function getContainer(): AbstractBlock
    {
        return $this->container;
    }

    public function getReferenceMap(): ReferenceMapInterface
    {
        return $this->referenceMap;
    }

    public function getCursor(): Cursor
    {
        return $this->cursor;
    }

    public function getDelimiterStack(): DelimiterStack
    {
        return $this->delimiterStack;
    }
}
