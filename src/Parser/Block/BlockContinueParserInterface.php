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

namespace League\CommonMark\Parser\Block;

use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Parser\Cursor;

/**
 * Interface for a block continuation parser
 *
 * A block continue parser can only handle a single block instance. The current block being parsed is stored within this parser and
 * can be returned once parsing has completed. If you need to parse multiple block continuations, instantiate a new parser for each one.
 */
interface BlockContinueParserInterface
{
    /**
     * Return the current block being parsed by this parser
     */
    public function getBlock(): AbstractBlock;

    /**
     * Return whether we are parsing a container block
     */
    public function isContainer(): bool;

    /**
     * Return whether the current line may be added to this block even though it wasn't continued by tryContinue()
     *
     * This is checked per line, after tryContinue() (which is skipped if an enclosing container did not match), so
     * the answer may depend on the current line. Block start parsers may also consult it to decide whether a new
     * block can interrupt this one.
     */
    public function canHaveLazyContinuationLines(): bool;

    /**
     * Determine whether the current block being parsed can contain the given child block
     */
    public function canContain(AbstractBlock $childBlock): bool;

    /**
     * Attempt to parse the given line
     */
    public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser): ?BlockContinue;

    /**
     * Add the given line of text to the current block
     */
    public function addLine(string $line): void;

    /**
     * Close and finalize the current block
     */
    public function closeBlock(): void;
}
