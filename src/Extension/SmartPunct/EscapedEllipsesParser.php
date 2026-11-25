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

namespace League\CommonMark\Extension\SmartPunct;

use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;

/**
 * Converts ellipses in a run of periods and spaces that starts with an escaped period.
 *
 * EllipsesParser scans such a run from the escaped period itself, but an escape consumes that period, so
 * the ellipses that should then be found are the ones a scan starting at the next period would find.
 * This parser provides those, without changing where EllipsesParser (or any other parser) stops.
 *
 * @internal
 */
final class EscapedEllipsesParser implements InlineParserInterface
{
    public function getMatchDefinition(): InlineParserMatch
    {
        // A period after one or three backslashes starts the run; \G then continues it from the end of the previous
        // match, which in this parser's own scan can only be an ellipsis from the same run
        return InlineParserMatch::regex('(?<=(?<!\\\\)\\\\|(?<!\\\\)\\\\\\\\\\\\)\\.[. ]*?\\K(?:\\.\\.\\.|\\. \\. \\.)|\\G(?<=[. ])[. ]*?\\K(?:\\.\\.\\.|\\. \\. \\.)');
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        if ($inlineContext->getCursor()->matchInPlace('/\\G(?:\\.\\.\\.|\\. \\. \\.)/') === null) {
            return false;
        }

        $inlineContext->getContainer()->appendChild(new Text('…'));

        return true;
    }
}
