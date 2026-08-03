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

namespace League\CommonMark\Parser;

use League\CommonMark\Exception\UnexpectedEncodingException;

class Cursor
{
    public const INDENT_LEVEL = 4;

    /**
     * Interval (in characters) between recorded byte-offset checkpoints on multibyte lines.
     * A larger interval uses less memory but makes random lookups walk further; a smaller one
     * costs more memory but speeds random lookups. Sequential access is O(1) regardless, via the
     * last-resolved-position cache, so this only trades memory against the cost of random jumps
     * (backwards peeks, saveState/restoreState).
     */
    private const BYTE_OFFSET_CHECKPOINT_INTERVAL = 16;

    /** @psalm-readonly */
    private string $line;

    /** @psalm-readonly */
    private int $length;

    /**
     * @var int
     *
     * It's possible for this to be 1 char past the end, meaning we've parsed all chars and have
     * reached the end.  In this state, any character-returning method MUST return null.
     */
    private int $currentPosition = 0;

    private int $column = 0;

    private int $indent = 0;

    private int $previousPosition = 0;

    private ?int $nextNonSpaceCache = null;

    private bool $partiallyConsumedTab = false;

    /**
     * @var int|false
     *
     * @psalm-readonly
     */
    private $lastTabPosition;

    /** @psalm-readonly */
    private bool $isMultibyte;

    /**
     * Sparse lookup table mapping a checkpoint index (character index divided by
     * BYTE_OFFSET_CHECKPOINT_INTERVAL) to the byte offset at which that character begins within
     * $line. Only populated for multibyte lines, and only ever extended forwards. Recording one
     * offset per interval - rather than one per character - bounds this map to O(n / interval)
     * memory, so it can never become the dominant allocation on a long line. A lookup walks at most
     * `interval` bytes from the nearest checkpoint (or O(1) from the last-resolved position for the
     * sequential access that dominates parsing); this is what keeps character<->byte translation -
     * and therefore every substring accessor - linear rather than O(n^2) on multibyte lines.
     *
     * @var array<int, int>
     */
    private array $byteOffsetCheckpoints = [];

    /** Character index of the most recently resolved byte offset (paired with $lastByteOffset). */
    private int $lastByteOffsetPosition = 0;

    /** Byte offset of the most recently resolved character index (paired with $lastByteOffsetPosition). */
    private int $lastByteOffset = 0;

    /**
     * Small cache of individually-read single characters on multibyte lines, keyed by
     * character index. Only populated for characters actually read one-at-a-time (e.g.
     * repeated peek() during delimiter scanning), so it stays tiny; substring accessors
     * bypass it entirely. Avoids re-slicing the same character on every lookup.
     *
     * @var array<int, string>
     */
    private array $charCache = [];

    /**
     * @param string $line The line being parsed (ASCII or UTF-8)
     */
    public function __construct(string $line)
    {
        if (! \mb_check_encoding($line, 'UTF-8')) {
            throw new UnexpectedEncodingException('Unexpected encoding - UTF-8 or ASCII was expected');
        }

        $this->line            = $line;
        $this->length          = \mb_strlen($line, 'UTF-8') ?: 0;
        $this->isMultibyte     = $this->length !== \strlen($line);
        $this->lastTabPosition = $this->isMultibyte ? \mb_strrpos($line, "\t", 0, 'UTF-8') : \strrpos($line, "\t");

        if (! $this->isMultibyte) {
            return;
        }

        // Seed the checkpoint map with the start of the line; it is extended on demand (see byteOffset()).
        $this->byteOffsetCheckpoints = [0];
    }

    /**
     * Translate a character index into its byte offset within $line.
     *
     * The scan starts from the closest known reference point - the last-resolved position when it
     * sits between the target and the nearest earlier checkpoint (the common sequential case, O(1)),
     * otherwise that checkpoint (at most `interval` characters away). It then walks forwards one
     * character at a time, skipping UTF-8 continuation bytes (0b10xxxxxx) and recording a checkpoint
     * every `interval` characters. Sequential access is therefore amortized O(1) and any random
     * lookup is bounded by O(interval), all while the map itself stays O(n / interval) in memory.
     */
    private function byteOffset(int $index): int
    {
        if ($index === $this->lastByteOffsetPosition) {
            return $this->lastByteOffset;
        }

        // Start from the checkpoint at or before $index, or - if we've not scanned that far yet -
        // from the furthest checkpoint recorded so far.
        $checkpoint = \intdiv($index, self::BYTE_OFFSET_CHECKPOINT_INTERVAL);
        if (! isset($this->byteOffsetCheckpoints[$checkpoint])) {
            $checkpoint = \count($this->byteOffsetCheckpoints) - 1;
        }

        $position = $checkpoint * self::BYTE_OFFSET_CHECKPOINT_INTERVAL;
        $byte     = $this->byteOffsetCheckpoints[$checkpoint];

        // Prefer the last-resolved position when it is a closer starting point (ahead of the chosen
        // checkpoint but not past the target). This makes sequential forward access O(1).
        if ($this->lastByteOffsetPosition > $position && $this->lastByteOffsetPosition <= $index) {
            $position = $this->lastByteOffsetPosition;
            $byte     = $this->lastByteOffset;
        }

        $lineBytes = \strlen($this->line);
        while ($position < $index) {
            $byte++;
            while ($byte < $lineBytes && (\ord($this->line[$byte]) & 0xC0) === 0x80) {
                $byte++;
            }

            $position++;
            if ($position % self::BYTE_OFFSET_CHECKPOINT_INTERVAL === 0) {
                $this->byteOffsetCheckpoints[\intdiv($position, self::BYTE_OFFSET_CHECKPOINT_INTERVAL)] = $byte;
            }
        }

        $this->lastByteOffsetPosition = $index;
        $this->lastByteOffset         = $byte;

        return $byte;
    }

    /**
     * Return the single multibyte character at $index, caching the sliced string so
     * repeated reads of the same position (common during delimiter scanning) don't
     * re-slice. Callers must ensure 0 <= $index < $length.
     */
    private function charAt(int $index): string
    {
        if (isset($this->charCache[$index])) {
            return $this->charCache[$index];
        }

        $startByte = $this->byteOffset($index);

        return $this->charCache[$index] = \substr($this->line, $startByte, $this->byteOffset($index + 1) - $startByte);
    }

    /**
     * Returns the position of the next character which is not a space (or tab)
     */
    public function getNextNonSpacePosition(): int
    {
        if ($this->nextNonSpaceCache !== null) {
            return $this->nextNonSpaceCache;
        }

        if ($this->currentPosition >= $this->length) {
            return $this->length;
        }

        $cols = $this->column;

        // Spaces (0x20) and tabs (0x09) are single-byte ASCII characters which can
        // never appear inside a multibyte UTF-8 sequence, so the leading run of
        // spaces/tabs is always scanned at the byte level.  For multibyte lines this
        // avoids calling mb_substr() once per character - each such call decodes from
        // the start of the string (O(i)), making the scan O(n^2) over a long run of
        // leading whitespace.  Because every character in the run occupies exactly one
        // byte, the character index and byte offset advance together.
        $byteOffset = $this->isMultibyte ? $this->byteOffset($this->currentPosition) : $this->currentPosition;

        for ($i = $this->currentPosition; $i < $this->length; $i++, $byteOffset++) {
            $c = $this->line[$byteOffset];

            if ($c === ' ') {
                $cols++;
            } elseif ($c === "\t") {
                $cols += 4 - ($cols % 4);
            } else {
                break;
            }
        }

        $this->indent = $cols - $this->column;

        return $this->nextNonSpaceCache = $i;
    }

    /**
     * Returns the next character which isn't a space (or tab)
     */
    public function getNextNonSpaceCharacter(): ?string
    {
        $index = $this->getNextNonSpacePosition();
        if ($index >= $this->length) {
            return null;
        }

        if ($this->isMultibyte) {
            return $this->charAt($index);
        }

        return $this->line[$index];
    }

    /**
     * Calculates the current indent (number of spaces after current position)
     */
    public function getIndent(): int
    {
        if ($this->nextNonSpaceCache === null) {
            $this->getNextNonSpacePosition();
        }

        return $this->indent;
    }

    /**
     * Whether the cursor is indented to INDENT_LEVEL
     */
    public function isIndented(): bool
    {
        if ($this->nextNonSpaceCache === null) {
            $this->getNextNonSpacePosition();
        }

        return $this->indent >= self::INDENT_LEVEL;
    }

    public function getCharacter(?int $index = null): ?string
    {
        if ($index === null) {
            $index = $this->currentPosition;
        }

        // Index out-of-bounds, or we're at the end
        if ($index < 0 || $index >= $this->length) {
            return null;
        }

        if ($this->isMultibyte) {
            return $this->charAt($index);
        }

        return $this->line[$index];
    }

    /**
     * Slightly-optimized version of getCurrent(null)
     */
    public function getCurrentCharacter(): ?string
    {
        if ($this->currentPosition >= $this->length) {
            return null;
        }

        if ($this->isMultibyte) {
            return $this->charAt($this->currentPosition);
        }

        return $this->line[$this->currentPosition];
    }

    /**
     * Returns the next character (or null, if none) without advancing forwards
     */
    public function peek(int $offset = 1): ?string
    {
        return $this->getCharacter($this->currentPosition + $offset);
    }

    /**
     * Whether the remainder is blank
     */
    public function isBlank(): bool
    {
        return $this->nextNonSpaceCache === $this->length || $this->getNextNonSpacePosition() === $this->length;
    }

    /**
     * Move the cursor forwards
     */
    public function advance(): void
    {
        $this->advanceBy(1);
    }

    /**
     * Move the cursor forwards
     *
     * @param int  $characters       Number of characters to advance by
     * @param bool $advanceByColumns Whether to advance by columns instead of spaces
     */
    public function advanceBy(int $characters, bool $advanceByColumns = false): void
    {
        $this->previousPosition  = $this->currentPosition;
        $this->nextNonSpaceCache = null;

        if ($this->currentPosition >= $this->length || $characters === 0) {
            return;
        }

        // Optimization to avoid tab handling logic if we have no tabs
        if ($this->lastTabPosition === false || $this->currentPosition > $this->lastTabPosition) {
            $length                     = \min($characters, $this->length - $this->currentPosition);
            $this->partiallyConsumedTab = false;
            $this->currentPosition     += $length;
            $this->column              += $length;

            return;
        }

        if ($this->isMultibyte) {
            $startByte    = $this->byteOffset($this->currentPosition);
            $endByte      = $this->byteOffset(\min($this->currentPosition + $characters, $this->length));
            $nextFewChars = \substr($this->line, $startByte, $endByte - $startByte);
        } else {
            $nextFewChars = \substr($this->line, $this->currentPosition, $characters);
        }

        if ($characters === 1) {
            $asArray = [$nextFewChars];
        } elseif ($this->isMultibyte) {
            /** @var list<string> $asArray */
            $asArray = \mb_str_split($nextFewChars, 1, 'UTF-8');
        } else {
            $asArray = \str_split($nextFewChars);
        }

        foreach ($asArray as $c) {
            if ($c === "\t") {
                $charsToTab = 4 - ($this->column % 4);
                if ($advanceByColumns) {
                    $this->partiallyConsumedTab = $charsToTab > $characters;
                    $charsToAdvance             = $charsToTab > $characters ? $characters : $charsToTab;
                    $this->column              += $charsToAdvance;
                    $this->currentPosition     += $this->partiallyConsumedTab ? 0 : 1;
                    $characters                -= $charsToAdvance;
                } else {
                    $this->partiallyConsumedTab = false;
                    $this->column              += $charsToTab;
                    $this->currentPosition++;
                    $characters--;
                }
            } else {
                $this->partiallyConsumedTab = false;
                $this->currentPosition++;
                $this->column++;
                $characters--;
            }

            if ($characters <= 0) {
                break;
            }
        }
    }

    /**
     * Advances the cursor by a single space or tab, if present
     */
    public function advanceBySpaceOrTab(): bool
    {
        $character = $this->getCurrentCharacter();

        if ($character === ' ' || $character === "\t") {
            $this->advanceBy(1, true);

            return true;
        }

        return false;
    }

    /**
     * Parse zero or more space/tab characters
     *
     * @return int Number of positions moved
     */
    public function advanceToNextNonSpaceOrTab(): int
    {
        $newPosition = $this->nextNonSpaceCache ?? $this->getNextNonSpacePosition();
        if ($newPosition === $this->currentPosition) {
            return 0;
        }

        $this->advanceBy($newPosition - $this->currentPosition);
        $this->partiallyConsumedTab = false;

        // We've just advanced to where that non-space is,
        // so any subsequent calls to find the next one will
        // always return the current position.
        $this->nextNonSpaceCache = $this->currentPosition;
        $this->indent            = 0;

        return $this->currentPosition - $this->previousPosition;
    }

    /**
     * Parse zero or more space characters, including at most one newline.
     *
     * Tab characters are not parsed with this function.
     *
     * @return int Number of positions moved
     */
    public function advanceToNextNonSpaceOrNewline(): int
    {
        $currentCharacter = $this->getCurrentCharacter();

        // Optimization: Avoid the regex if we know there are no spaces or newlines
        if ($currentCharacter !== ' ' && $currentCharacter !== "\n") {
            $this->previousPosition = $this->currentPosition;

            return 0;
        }

        $matches = [];
        \preg_match('/^ *(?:\n *)?/', $this->getRemainder(), $matches, \PREG_OFFSET_CAPTURE);

        // [0][0] contains the matched text
        // [0][1] contains the index of that match
        \assert(isset($matches[0]));
        $increment = $matches[0][1] + \strlen($matches[0][0]);

        $this->advanceBy($increment);

        return $this->currentPosition - $this->previousPosition;
    }

    /**
     * Move the position to the very end of the line
     *
     * @return int The number of characters moved
     */
    public function advanceToEnd(): int
    {
        $this->previousPosition  = $this->currentPosition;
        $this->nextNonSpaceCache = null;

        $this->currentPosition = $this->length;

        return $this->currentPosition - $this->previousPosition;
    }

    public function getRemainder(): string
    {
        if ($this->currentPosition >= $this->length) {
            return '';
        }

        $prefix   = '';
        $position = $this->currentPosition;
        if ($this->partiallyConsumedTab) {
            $position++;
            $charsToTab = 4 - ($this->column % 4);
            $prefix     = \str_repeat(' ', $charsToTab);
        }

        $subString = $this->isMultibyte ?
            \substr($this->line, $this->byteOffset($position)) :
            \substr($this->line, $position);

        return $prefix . $subString;
    }

    public function getLine(): string
    {
        return $this->line;
    }

    public function isAtEnd(): bool
    {
        return $this->currentPosition >= $this->length;
    }

    /**
     * Try to match a regular expression
     *
     * Returns the matching text and advances to the end of that match
     *
     * @psalm-param non-empty-string $regex
     */
    public function match(string $regex): ?string
    {
        // When a tab has been partially consumed the remainder is reconstructed with the
        // leftover tab expanded into spaces, so matching must run against that reconstructed
        // string rather than the raw line. This is rare; use the copy-based path to preserve
        // the exact column arithmetic.
        if ($this->partiallyConsumedTab) {
            return $this->matchViaRemainder($regex);
        }

        // Match against the persistent line at the current byte offset instead of allocating a
        // fresh copy of the remainder on every call. A leading "^" is rewritten to "\G" so the
        // pattern still anchors to the cursor - a bare "^" only matches at the true start of the
        // subject when a non-zero offset is supplied. Patterns that intentionally scan ahead
        // (e.g. the backtick closer search) carry no leading "^" and are left untouched. This
        // keeps repeated match() calls - such as that backtick scan - linear rather than O(n^2),
        // since each call no longer copies the entire remaining line.
        if ($regex[1] === '^') {
            $regex = $regex[0] . '\\G' . \substr($regex, 2);
        }

        $bytePosition = $this->isMultibyte ? $this->byteOffset($this->currentPosition) : $this->currentPosition;

        if (! \preg_match($regex, $this->line, $matches, \PREG_OFFSET_CAPTURE, $bytePosition)) {
            return null;
        }

        // $matches[0][0] contains the matched text; $matches[0][1] is its absolute byte offset in the line.
        if ($this->isMultibyte) {
            // Convert the byte offset to a character advance relative to the cursor. The scanned gap
            // is only the distance from the cursor to the match (zero for anchored patterns), never
            // the whole line, so this stays linear across repeated calls.
            $offset      = \mb_strlen(\substr($this->line, $bytePosition, $matches[0][1] - $bytePosition), 'UTF-8');
            $matchLength = \mb_strlen($matches[0][0], 'UTF-8');
        } else {
            $offset      = $matches[0][1] - $this->currentPosition;
            $matchLength = \strlen($matches[0][0]);
        }

        $this->advanceBy($offset + $matchLength);

        return $matches[0][0];
    }

    /**
     * Slow path for match() used only when a tab has been partially consumed: match against a
     * freshly-built remainder whose leftover tab is expanded into spaces, advancing by columns
     * across that expansion. Kept separate so the common case avoids the remainder allocation.
     *
     * @psalm-param non-empty-string $regex
     */
    private function matchViaRemainder(string $regex): ?string
    {
        $subject = $this->getRemainder();

        if (! \preg_match($regex, $subject, $matches, \PREG_OFFSET_CAPTURE)) {
            return null;
        }

        if ($this->isMultibyte) {
            $offset      = \mb_strlen(\substr($subject, 0, $matches[0][1]), 'UTF-8');
            $matchLength = \mb_strlen($matches[0][0], 'UTF-8');
        } else {
            $offset      = $matches[0][1];
            $matchLength = \strlen($matches[0][0]);
        }

        $advance = $offset + $matchLength;

        // The remainder we matched against had the partially-consumed tab expanded into spaces,
        // so those columns must be advanced by column instead of by character.
        $charsToTab = 4 - ($this->column % 4);
        if ($advance < $charsToTab) {
            $this->advanceBy($advance, true);

            return $matches[0][0];
        }

        $this->advanceBy($charsToTab, true);
        $advance -= $charsToTab;

        $this->advanceBy($advance);

        return $matches[0][0];
    }

    /**
     * Encapsulates the current state of this cursor in case you need to rollback later.
     *
     * WARNING: Do not parse or use the return value for ANYTHING except for
     * passing it back into restoreState(), as the number of values and their
     * contents may change in any future release without warning.
     */
    public function saveState(): CursorState
    {
        return new CursorState([
            $this->currentPosition,
            $this->previousPosition,
            $this->nextNonSpaceCache,
            $this->indent,
            $this->column,
            $this->partiallyConsumedTab,
        ]);
    }

    /**
     * Restore the cursor to a previous state.
     *
     * Pass in the value previously obtained by calling saveState().
     */
    public function restoreState(CursorState $state): void
    {
        [
            $this->currentPosition,
            $this->previousPosition,
            $this->nextNonSpaceCache,
            $this->indent,
            $this->column,
            $this->partiallyConsumedTab,
        ] = $state->toArray();
    }

    public function getPosition(): int
    {
        return $this->currentPosition;
    }

    /**
     * Returns the byte offset of the current position within the line.
     *
     * For single-byte lines this is identical to getPosition(). For multibyte
     * lines the offset comes from the lazily-built character->byte map, which
     * makes this an amortized-O(1) lookup rather than O(position) per call.
     */
    public function getBytePosition(): int
    {
        if (! $this->isMultibyte) {
            return $this->currentPosition;
        }

        return $this->byteOffset($this->currentPosition);
    }

    public function getPreviousText(): string
    {
        if ($this->isMultibyte) {
            $startByte = $this->byteOffset($this->previousPosition);

            return \substr($this->line, $startByte, $this->byteOffset($this->currentPosition) - $startByte);
        }

        return \substr($this->line, $this->previousPosition, $this->currentPosition - $this->previousPosition);
    }

    public function getSubstring(int $start, ?int $length = null): string
    {
        if ($this->isMultibyte) {
            // Negative offsets/lengths are rare (and never used internally); defer to mb_substr
            // so its exact semantics are preserved.
            if ($start < 0 || ($length !== null && $length < 0)) {
                return \mb_substr($this->line, $start, $length, 'UTF-8');
            }

            if ($start >= $this->length) {
                return '';
            }

            $startByte = $this->byteOffset($start);

            if ($length === null) {
                return \substr($this->line, $startByte);
            }

            return \substr($this->line, $startByte, $this->byteOffset($start + \min($length, $this->length - $start)) - $startByte);
        }

        if ($length !== null) {
            return \substr($this->line, $start, $length);
        }

        return \substr($this->line, $start);
    }

    public function getColumn(): int
    {
        return $this->column;
    }
}
