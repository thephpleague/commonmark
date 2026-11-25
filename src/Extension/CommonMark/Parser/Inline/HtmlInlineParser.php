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
     * Comments, processing instructions, CDATA sections and declarations may run to the end of
     * the paragraph, so their ends are located with strpos() rather than a lazy regex, which costs
     * O(n) per opener and is quadratic without PCRE JIT.
     */
    private const TERMINATORS = ['<!--' => '-->', '<?' => '?>', '<![CDATA[' => ']]>', '<!A' => '>'];

    /** @var \WeakReference<Cursor>|null */
    private ?\WeakReference $cursor = null;

    /**
     * Byte offset of the last occurrence of each terminator in the cursor's line, so an opener
     * with no terminator after it is rejected in O(1).
     *
     * @var array<string, int|false>
     */
    private array $lastTerminator = [];

    private const TAG_REGEX = '/\\G(?:' . RegexHelper::PARTIAL_OPENTAG . '|' . RegexHelper::PARTIAL_CLOSETAG . ')/';

    public function getMatchDefinition(): InlineParserMatch
    {
        // Only the "<" is matched here: preg_match_all() returns non-overlapping matches, so a
        // longer match could hide a tag that starts inside it
        return InlineParserMatch::regex('<(?=[A-Za-z\\/!?])');
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $cursor = $inlineContext->getCursor();
        $line   = $cursor->getLine();
        $start  = $cursor->getBytePosition();
        $next   = $line[$start + 1] ?? '';

        // Openers are recognized without a regex, so floods of them stay cheap
        if ($next === '!' || $next === '?') {
            $inline = $this->matchUntilTerminator($cursor, $line, $start);
        } elseif (\preg_match(self::TAG_REGEX, $line, $m, 0, $start) === 1) {
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

    private function matchUntilTerminator(Cursor $cursor, string $line, int $start): ?string
    {
        if (\substr_compare($line, '<!--', $start, 4) === 0) {
            foreach (['<!-->', '<!--->'] as $emptyComment) {
                if (\substr_compare($line, $emptyComment, $start, \strlen($emptyComment)) === 0) {
                    return $emptyComment;
                }
            }

            $opener = '<!--';
        } elseif ($line[$start + 1] === '?') {
            $opener = '<?';
        } elseif (\substr_compare($line, '<![CDATA[', $start, 9) === 0) {
            $opener = '<![CDATA[';
        } elseif (self::isAsciiLetter($line[$start + 2] ?? '')) {
            $opener = '<!A';
        } else {
            return null;
        }

        // Declarations are matched up to their first letter
        $terminator = self::TERMINATORS[$opener];
        $from       = $start + \strlen($opener);

        if ($this->cursor === null || $this->cursor->get() !== $cursor) {
            $this->cursor         = \WeakReference::create($cursor);
            $this->lastTerminator = [];
        }

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
