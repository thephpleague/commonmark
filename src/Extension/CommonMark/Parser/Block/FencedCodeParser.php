<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\CommonMark\Parser\Block;

use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Parser\Block\AbstractBlockContinueParser;
use League\CommonMark\Parser\Block\BlockContinue;
use League\CommonMark\Parser\Block\BlockContinueParserInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Util\ArrayCollection;
use League\CommonMark\Util\RegexHelper;

final class FencedCodeParser extends AbstractBlockContinueParser
{
    /**
     * @var FencedCode
     *
     * @psalm-readonly
     */
    private $block;

    /** @var ArrayCollection<string> */
    protected $strings;

    public function __construct(int $fenceLength, string $fenceChar, int $fenceOffset)
    {
        $this->block   = new FencedCode($fenceLength, $fenceChar, $fenceOffset);
        $this->strings = new ArrayCollection();
    }

    /**
     * @return FencedCode
     */
    public function getBlock(): AbstractBlock
    {
        return $this->block;
    }

    public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser): ?BlockContinue
    {
        // Check for closing code fence
        if (! $cursor->isIndented() && $cursor->getNextNonSpaceCharacter() === $this->block->getChar()) {
            $match = RegexHelper::matchFirst('/^(?:`{3,}|~{3,})(?= *$)/', $cursor->getLine(), $cursor->getNextNonSpacePosition());
            if ($match !== null && \strlen($match[0]) >= $this->block->getLength()) {
                // closing fence - we're at end of line, so we can finalize now
                return BlockContinue::finished();
            }
        }

        // Skip optional spaces of fence offset
        $cursor->match('/^ {0,' . $this->block->getOffset() . '}/');

        return BlockContinue::at($cursor);
    }

    public function addLine(string $line): void
    {
        $this->strings[] = $line;
    }

    public function closeBlock(): void
    {
        // first line becomes info string
        $firstLine = $this->strings->first();
        if ($firstLine === false) {
            $firstLine = '';
        }

        $this->block->setInfo(RegexHelper::unescape(\trim($firstLine)));

        if ($this->strings->count() === 1) {
            $this->block->setLiteral('');
        } else {
            $this->block->setLiteral(\implode("\n", $this->strings->slice(1)) . "\n");
        }
    }
}
