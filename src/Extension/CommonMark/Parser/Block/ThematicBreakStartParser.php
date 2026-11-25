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

use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\MarkdownParserStateInterface;

final class ThematicBreakStartParser implements BlockStartParserInterface
{
    public function tryStart(Cursor $cursor, MarkdownParserStateInterface $parserState): ?BlockStart
    {
        if ($cursor->isIndented()) {
            return BlockStart::none();
        }

        // No regex here: a line of markers can be long enough to hit PCRE's limits
        $rest   = \ltrim(\substr($cursor->getLine(), $cursor->getBytePosition()), " \t");
        $marker = $rest[0] ?? '';
        if ($marker !== '*' && $marker !== '-' && $marker !== '_') {
            return BlockStart::none();
        }

        if (\strspn($rest, $marker . " \t") !== \strlen($rest) || \substr_count($rest, $marker) < 3) {
            return BlockStart::none();
        }

        // Advance to the end of the string, consuming the entire line (of the thematic break)
        $cursor->advanceToEnd();

        return BlockStart::of(new ThematicBreakParser())->at($cursor);
    }
}
