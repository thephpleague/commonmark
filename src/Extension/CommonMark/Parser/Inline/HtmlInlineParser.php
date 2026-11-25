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

namespace League\CommonMark\Extension\CommonMark\Parser\Inline;

use League\CommonMark\Extension\CommonMark\Node\Inline\HtmlInline;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;
use League\CommonMark\Util\RegexHelper;

final class HtmlInlineParser implements InlineParserInterface
{
    /**
     * Anchored at the cursor; without (*NO_START_OPT), PCRE would first search the rest of the
     * line for the required ">" on every "<"
     */
    private const TAG_REGEX = '/(*NO_START_OPT)\G(?:' . RegexHelper::PARTIAL_OPENTAG . '|' . RegexHelper::PARTIAL_CLOSETAG . ')/';

    /**
     * State for the paragraph currently being parsed
     *
     * @var \WeakReference<Cursor>|null
     */
    private ?\WeakReference $cursor = null;

    /** @psalm-var non-empty-string */
    private string $tagRegex = self::TAG_REGEX;

    /**
     * Byte offset of the last occurrence of each terminator, so an opener with no terminator
     * after it is rejected in O(1)
     *
     * @var array<string, int|false>
     */
    private array $lastTerminator = [];

    public function getMatchDefinition(): InlineParserMatch
    {
        // Only the "<" is matched: preg_match_all() returns non-overlapping matches, so a longer
        // match could hide a tag that starts inside it
        return InlineParserMatch::regex('<(?=[A-Za-z\/!?])')->caseSensitive();
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $cursor = $inlineContext->getCursor();
        $line   = $cursor->getLine();
        $start  = $cursor->getBytePosition();

        if ($this->cursor === null || $this->cursor->get() !== $cursor) {
            $this->cursor         = \WeakReference::create($cursor);
            $this->lastTerminator = [];
            // As when the engine ran this regex, \s also matches Unicode whitespace on non-ASCII lines
            $this->tagRegex = self::TAG_REGEX . (\mb_check_encoding($line, 'ASCII') ? '' : 'u');
        }

        $next = $line[$start + 1] ?? '';
        if ($next === '!' || $next === '?') {
            $inline = $this->matchUntilTerminator($line, $start);
        } elseif (\preg_match($this->tagRegex, $line, $m, 0, $start) === 1) {
            $inline = $m[0];
        } else {
            $inline = null;
        }

        if ($inline === null) {
            return false;
        }

        $cursor->advanceBy(\mb_strlen($inline, 'UTF-8'));
        $inlineContext->getContainer()->appendChild(new HtmlInline($inline));

        return true;
    }

    /**
     * Comments, processing instructions, CDATA sections and declarations may run to the end of the
     * paragraph, so their ends are found with strpos(); a lazy regex would rescan the rest of the
     * paragraph from every opener, which is quadratic without PCRE JIT
     */
    private function matchUntilTerminator(string $line, int $start): ?string
    {
        if (\substr_compare($line, '<!--', $start, 4) === 0) {
            foreach (['<!-->', '<!--->'] as $emptyComment) {
                if (\substr_compare($line, $emptyComment, $start, \strlen($emptyComment)) === 0) {
                    return $emptyComment;
                }
            }

            [$openerLength, $terminator] = [4, '-->'];
        } elseif ($line[$start + 1] === '?') {
            [$openerLength, $terminator] = [2, '?>'];
        } elseif (\substr_compare($line, '<![CDATA[', $start, 9) === 0) {
            [$openerLength, $terminator] = [9, ']]>'];
        } elseif (self::isAsciiLetter($line[$start + 2] ?? '')) {
            // A declaration: "<!" and a letter
            [$openerLength, $terminator] = [3, '>'];
        } else {
            return null;
        }

        $from = $start + $openerLength;
        $last = $this->lastTerminator[$terminator] ??= \strrpos($line, $terminator);
        if ($last === false || $last < $from) {
            return null;
        }

        $end = \strpos($line, $terminator, $from);
        \assert($end !== false);

        return \substr($line, $start, $end + \strlen($terminator) - $start);
    }

    private static function isAsciiLetter(string $char): bool
    {
        return ($char >= 'a' && $char <= 'z') || ($char >= 'A' && $char <= 'Z');
    }
}
