<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 * (c) Rezo Zero / Ambroise Maupate
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\CommonMark\Extension\Footnote\Parser;

use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\MarkdownParserStateInterface;
use League\CommonMark\Reference\Reference;

final class FootnoteStartParser implements BlockStartParserInterface
{
    private const REGEX_DEFINITION = '/^\[\^([^\s^\]]+)\]\:(?:\s|$)/';

    public function tryStart(Cursor $cursor, MarkdownParserStateInterface $parserState): ?BlockStart
    {
        if ($cursor->isIndented() || $parserState->getLastMatchedBlockParser()->canHaveLazyContinuationLines()) {
            return BlockStart::none();
        }

        $cursor->advanceToNextNonSpaceOrTab();

        // Cursor positions count characters, not bytes, so let the cursor advance past the match itself
        $match = $cursor->match(self::REGEX_DEFINITION);
        if ($match === null || \preg_match(self::REGEX_DEFINITION, $match, $matches) !== 1) {
            return BlockStart::none();
        }

        $reference      = new Reference($matches[1], $matches[1], $matches[1]);
        $footnoteParser = new FootnoteParser($reference);

        return BlockStart::of($footnoteParser)->at($cursor);
    }
}
