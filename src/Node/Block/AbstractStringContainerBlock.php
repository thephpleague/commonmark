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

namespace League\CommonMark\Node\Block;

use League\CommonMark\Parser\ContextInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Util\ArrayCollection;

/**
 * @method children() AbstractInline[]
 */
abstract class AbstractStringContainerBlock extends AbstractBlock implements StringContainerInterface
{
    /**
     * @var ArrayCollection<int, string>
     */
    protected $strings;

    /**
     * @var string
     */
    protected $finalStringContents = '';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->strings = new ArrayCollection();
    }

    public function addLine(string $line): void
    {
        $this->strings[] = $line;
    }

    abstract public function handleRemainingContents(ContextInterface $context, Cursor $cursor): void;

    public function getStringContent(): string
    {
        return $this->finalStringContents;
    }
}
