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

namespace League\CommonMark\Extension\CommonMark\Node\Block;

use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Node\Block\AbstractStringContainerBlock;
use League\CommonMark\Node\Block\InlineContainerInterface;
use League\CommonMark\Parser\ContextInterface;
use League\CommonMark\Parser\Cursor;

class Heading extends AbstractStringContainerBlock implements InlineContainerInterface
{
    /**
     * @var int
     */
    protected $level;

    /**
     * @param int             $level
     * @param string|string[] $contents
     */
    public function __construct(int $level, $contents)
    {
        parent::__construct();

        $this->level = $level;

        if (!\is_array($contents)) {
            $contents = [$contents];
        }

        foreach ($contents as $line) {
            $this->addLine($line);
        }
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    public function finalize(ContextInterface $context, int $endLineNumber): void
    {
        parent::finalize($context, $endLineNumber);

        $this->finalStringContents = \implode("\n", $this->strings->toArray());
    }

    public function canContain(AbstractBlock $block): bool
    {
        return false;
    }

    public function isCode(): bool
    {
        return false;
    }

    public function matchesNextLine(Cursor $cursor): bool
    {
        return false;
    }

    public function handleRemainingContents(ContextInterface $context, Cursor $cursor): void
    {
        // nothing to do; contents were already added via the constructor.
    }
}
