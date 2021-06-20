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

namespace League\CommonMark\Tests\Unit\Parser\Inline;

use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;

final class FakeInlineParser implements InlineParserInterface
{
    /** @var string[] */
    private array $matches = [];

    private InlineParserMatch $start;

    public function __construct(InlineParserMatch $start)
    {
        $this->start = $start;
    }

    public function getMatchDefinition(): InlineParserMatch
    {
        return $this->start;
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $match           = $inlineContext->getFullMatch();
        $this->matches[] = $match;

        $inlineContext->getCursor()->advanceBy(\mb_strlen($match));
        $inlineContext->getContainer()->appendChild(new Text($match));

        return true;
    }

    /**
     * @return string[]
     */
    public function getMatches(): array
    {
        return $this->matches;
    }
}
