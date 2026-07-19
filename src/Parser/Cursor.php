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

    private const BYTE_OFFSET_CHECKPOINT_INTERVAL = 64;

    // Native mbstring calls are faster for sparse access. The fixed cap keeps
    // their combined worst-case cost linear before checkpoints take over.
    private const NATIVE_MULTIBYTE_ACCESS_LIMIT = 128;

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

    private int $nativeMultibyteAccesses = 0;

    /**
     * Lazily-built byte offsets for character positions at fixed intervals.
     *
     * @var array<int, int>
     */
    private array $byteOffsetCheckpoints = [];

    private int $lastByteOffsetPosition = 0;

    private int $lastByteOffset = 0;

    /**
     * Cache of individually-read characters on multibyte lines, keyed by character index.
     * This avoids re-slicing characters which are read repeatedly during delimiter scanning.
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
    }

    /**
     * Use native multibyte operations while accesses are sparse, then switch permanently to checkpoints.
     */
    private function useNativeMultibyteFunctions(): bool
    {
        if ($this->byteOffsetCheckpoints !== []) {
            return false;
        }

        if ($this->nativeMultibyteAccesses < self::NATIVE_MULTIBYTE_ACCESS_LIMIT) {
            $this->nativeMultibyteAccesses++;

            return true;
        }

        $this->byteOffsetCheckpoints  = [0];
        $this->lastByteOffsetPosition = 0;
        $this->lastByteOffset         = 0;

        return false;
    }

    /**
     * Translate a character position to a byte offset, extending the map forwards as needed.
     */
    private function byteOffset(int $index): int
    {
        if ($this->byteOffsetCheckpoints === []) {
            $this->byteOffsetCheckpoints  = [0];
            $this->lastByteOffsetPosition = 0;
            $this->lastByteOffset         = 0;
        }

        if ($index === $this->lastByteOffsetPosition) {
            return $this->lastByteOffset;
        }

        $checkpoint         = \intdiv($index, self::BYTE_OFFSET_CHECKPOINT_INTERVAL);
        $checkpointPosition = $checkpoint * self::BYTE_OFFSET_CHECKPOINT_INTERVAL;
        if (! isset($this->byteOffsetCheckpoints[$checkpoint])) {
            $checkpoint         = \count($this->byteOffsetCheckpoints) - 1;
            $checkpointPosition = $checkpoint * self::BYTE_OFFSET_CHECKPOINT_INTERVAL;
        }

        $position = $checkpointPosition;
        $byte     = $this->byteOffsetCheckpoints[$checkpoint];

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
            if ($position % self::BYTE_OFFSET_CHECKPOINT_INTERVAL !== 0) {
                continue;
            }

            $this->byteOffsetCheckpoints[\intdiv($position, self::BYTE_OFFSET_CHECKPOINT_INTERVAL)] = $byte;
        }

        $this->lastByteOffsetPosition = $index;
        $this->lastByteOffset         = $byte;

        return $byte;
    }

    private function multibyteByteOffset(int $index): int
    {
        if ($this->useNativeMultibyteFunctions()) {
            return \strlen(\mb_substr($this->line, 0, $index, 'UTF-8'));
        }

        return $this->byteOffset($index);
    }

    private function multibyteSubstring(int $start, ?int $length = null): string
    {
        // Preserve mb_substr() semantics for negative offsets and lengths.
        if ($start < 0 || ($length !== null && $length < 0) || $this->useNativeMultibyteFunctions()) {
            return \mb_substr($this->line, $start, $length, 'UTF-8');
        }

        if ($start >= $this->length) {
            return '';
        }

        if ($length === null) {
            return \substr($this->line, $this->byteOffset($start));
        }

        $startByte = $this->byteOffset($start);
        $end       = $start + \min($length, $this->length - $start);

        return \substr($this->line, $startByte, $this->byteOffset($end) - $startByte);
    }

    /**
     * Return and cache the multibyte character at the given position.
     */
    private function charAt(int $index): string
    {
        if (isset($this->charCache[$index])) {
            return $this->charCache[$index];
        }

        return $this->charCache[$index] = $this->multibyteSubstring($index, 1);
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

        // Spaces and tabs are single-byte characters, so their character and byte positions
        // advance together. This avoids repeatedly scanning a multibyte line from its start.
        if (! $this->isMultibyte || $this->currentPosition === 0) {
            $byteOffset = $this->currentPosition;
        } else {
            $byteOffset = $this->multibyteByteOffset($this->currentPosition);
        }

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

        $nextFewChars = $this->isMultibyte ?
            $this->multibyteSubstring($this->currentPosition, $characters) :
            \substr($this->line, $this->currentPosition, $characters);

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
            $this->multibyteSubstring($position) :
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
        $subject = $this->getRemainder();

        if (! \preg_match($regex, $subject, $matches, \PREG_OFFSET_CAPTURE)) {
            return null;
        }

        // $matches[0][0] contains the matched text
        // $matches[0][1] contains the index of that match

        if ($this->isMultibyte) {
            // PREG_OFFSET_CAPTURE always returns the byte offset, not the char offset, which is annoying
            $offset      = \mb_strlen(\substr($subject, 0, $matches[0][1]), 'UTF-8');
            $matchLength = \mb_strlen($matches[0][0], 'UTF-8');
        } else {
            $offset      = $matches[0][1];
            $matchLength = \strlen($matches[0][0]);
        }

        $advance = $offset + $matchLength;

        // The remainder we matched against had any partially-consumed tab expanded into spaces,
        // so those columns must be advanced by column instead of by character
        if ($this->partiallyConsumedTab) {
            $charsToTab = 4 - ($this->column % 4);
            if ($advance < $charsToTab) {
                $this->advanceBy($advance, true);

                return $matches[0][0];
            }

            $this->advanceBy($charsToTab, true);
            $advance -= $charsToTab;
        }

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
     * lines sparse lookups use native multibyte functions before repeated
     * lookups switch to lazily-built character-to-byte checkpoints.
     */
    public function getBytePosition(): int
    {
        if (! $this->isMultibyte) {
            return $this->currentPosition;
        }

        return $this->multibyteByteOffset($this->currentPosition);
    }

    public function getPreviousText(): string
    {
        if ($this->isMultibyte) {
            return $this->multibyteSubstring($this->previousPosition, $this->currentPosition - $this->previousPosition);
        }

        return \substr($this->line, $this->previousPosition, $this->currentPosition - $this->previousPosition);
    }

    public function getSubstring(int $start, ?int $length = null): string
    {
        if ($this->isMultibyte) {
            return $this->multibyteSubstring($start, $length);
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
