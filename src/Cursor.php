<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark;

class Cursor
{
    const INDENT_LEVEL = 4;

    /**
     * @var string
     */
    private $line;

    /**
     * @var int
     */
    private $length;

    /**
     * @var int
     *
     * It's possible for this to be 1 char past the end, meaning we've parsed all chars and have
     * reached the end.  In this state, any character-returning method MUST return null.
     */
    private $currentPosition = 0;

    /**
     * @var int
     */
    private $column = 0;

    /**
     * @var int
     */
    private $indent = 0;

    /**
     * @var int
     */
    private $previousPosition = 0;

    /**
     * @var int|null
     */
    private $nextNonSpaceCache;

    /**
     * @var bool
     */
    private $partiallyConsumedTab = false;

    /**
     * @var string
     */
    private $encoding;

    /**
     * @var bool
     */
    private $lineContainsTabs;

    /**
     * @var bool
     */
    private $isMultibyte;

    /**
     * @param string $line
     */
    public function __construct($line)
    {
        $this->line = $line;
        $this->encoding = mb_detect_encoding($line, 'ASCII,UTF-8', true) ?: 'ISO-8859-1';
        $this->length = mb_strlen($line, $this->encoding);
        $this->isMultibyte = $this->length !== strlen($line);
        $this->lineContainsTabs = preg_match('/\t/', $line) > 0;
    }

    /**
     * Returns the position of the next character which is not a space (or tab)
     *
     * @return int
     */
    public function getNextNonSpacePosition()
    {
        if ($this->nextNonSpaceCache !== null) {
            return $this->nextNonSpaceCache;
        }

        $i = $this->currentPosition;
        $cols = $this->column;

        while (($c = $this->getCharacter($i)) !== null) {
            if ($c === ' ') {
                $i++;
                $cols++;
            } elseif ($c === "\t") {
                $i++;
                $cols += (4 - ($cols % 4));
            } else {
                break;
            }
        }

        $nextNonSpace = ($c === null) ? $this->length : $i;
        $this->indent = $cols - $this->column;

        return $this->nextNonSpaceCache = $nextNonSpace;
    }

    /**
     * Returns the next character which isn't a space (or tab)
     *
     * @return string
     */
    public function getNextNonSpaceCharacter()
    {
        return $this->getCharacter($this->getNextNonSpacePosition());
    }

    /**
     * Calculates the current indent (number of spaces after current position)
     *
     * @return int
     */
    public function getIndent()
    {
        $this->getNextNonSpacePosition();

        return $this->indent;
    }

    /**
     * Whether the cursor is indented to INDENT_LEVEL
     *
     * @return bool
     */
    public function isIndented()
    {
        $this->getNextNonSpacePosition();

        return $this->indent >= self::INDENT_LEVEL;
    }

    /**
     * @param int|null $index
     *
     * @return string|null
     */
    public function getCharacter($index = null)
    {
        if ($index === null) {
            $index = $this->currentPosition;
        }

        // Index out-of-bounds, or we're at the end
        if ($index < 0 || $index >= $this->length) {
            return;
        }

        return mb_substr($this->line, $index, 1, $this->encoding);
    }

    /**
     * Returns the next character (or null, if none) without advancing forwards
     *
     * @param int $offset
     *
     * @return string|null
     */
    public function peek($offset = 1)
    {
        return $this->getCharacter($this->currentPosition + $offset);
    }

    /**
     * Whether the remainder is blank
     *
     * @return bool
     */
    public function isBlank()
    {
        return $this->getNextNonSpacePosition() === $this->length;
    }

    /**
     * Move the cursor forwards
     */
    public function advance()
    {
        $this->advanceBy(1);
    }

    /**
     * Move the cursor forwards
     *
     * @param int  $characters       Number of characters to advance by
     * @param bool $advanceByColumns Whether to advance by columns instead of spaces
     */
    public function advanceBy($characters, $advanceByColumns = false)
    {
        if ($characters === 0) {
            $this->previousPosition = $this->currentPosition;

            return;
        }

        $this->previousPosition = $this->currentPosition;
        $this->nextNonSpaceCache = null;

        $nextFewChars = mb_substr($this->line, $this->currentPosition, $characters, $this->encoding);

        // Optimization to avoid tab handling logic if we have no tabs
        if (!$this->lineContainsTabs || preg_match('/\t/', $nextFewChars) === 0) {
            $length = min($characters, $this->length - $this->currentPosition);
            $this->partiallyConsumedTab = false;
            $this->currentPosition += $length;
            $this->column += $length;

            return;
        }

        if ($characters === 1 && !empty($nextFewChars)) {
            $asArray = [$nextFewChars];
        } else {
            $asArray = preg_split('//u', $nextFewChars, null, PREG_SPLIT_NO_EMPTY);
        }

        foreach ($asArray as $relPos => $c) {
            if ($c === "\t") {
                $charsToTab = 4 - ($this->column % 4);
                if ($advanceByColumns) {
                    $this->partiallyConsumedTab = $charsToTab > $characters;
                    $charsToAdvance = $charsToTab > $characters ? $characters : $charsToTab;
                    $this->column += $charsToAdvance;
                    $this->currentPosition += $this->partiallyConsumedTab ? 0 : 1;
                    $characters -= $charsToAdvance;
                } else {
                    $this->partiallyConsumedTab = false;
                    $this->column += $charsToTab;
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
     *
     * @return bool
     */
    public function advanceBySpaceOrTab()
    {
        $character = $this->getCharacter();

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
    public function advanceToNextNonSpaceOrTab()
    {
        $newPosition = $this->getNextNonSpacePosition();
        $this->advanceBy($newPosition - $this->currentPosition);
        $this->partiallyConsumedTab = false;

        return $this->currentPosition - $this->previousPosition;
    }

    /**
     * Parse zero or more space characters, including at most one newline.
     *
     * Tab characters are not parsed with this function.
     *
     * @return int Number of positions moved
     */
    public function advanceToNextNonSpaceOrNewline()
    {
        $matches = [];
        preg_match('/^ *(?:\n *)?/', $this->getRemainder(), $matches, PREG_OFFSET_CAPTURE);

        // [0][0] contains the matched text
        // [0][1] contains the index of that match
        $increment = $matches[0][1] + strlen($matches[0][0]);

        if ($increment === 0) {
            return 0;
        }

        $this->advanceBy($increment);

        return $this->currentPosition - $this->previousPosition;
    }

    /**
     * Move the position to the very end of the line
     *
     * @return int The number of characters moved
     */
    public function advanceToEnd()
    {
        $this->previousPosition = $this->currentPosition;
        $this->nextNonSpaceCache = null;

        $this->currentPosition = $this->length;

        return $this->currentPosition - $this->previousPosition;
    }

    /**
     * @return string
     */
    public function getRemainder()
    {
        if ($this->currentPosition >= $this->length) {
            return '';
        }

        $prefix = '';
        $position = $this->currentPosition;
        if ($this->partiallyConsumedTab) {
            $position++;
            $charsToTab = 4 - ($this->column % 4);
            $prefix = str_repeat(' ', $charsToTab);
        }

        return $prefix . mb_substr($this->line, $position, null, $this->encoding);
    }

    /**
     * @return string
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @return bool
     */
    public function isAtEnd()
    {
        return $this->currentPosition >= $this->length;
    }

    /**
     * Try to match a regular expression
     *
     * Returns the matching text and advances to the end of that match
     *
     * @param string $regex
     *
     * @return string|null
     */
    public function match($regex)
    {
        $subject = $this->getRemainder();

        if (!preg_match($regex, $subject, $matches, PREG_OFFSET_CAPTURE)) {
            return;
        }

        // $matches[0][0] contains the matched text
        // $matches[0][1] contains the index of that match

        if ($this->isMultibyte) {
            // PREG_OFFSET_CAPTURE always returns the byte offset, not the char offset, which is annoying
            $offset = mb_strlen(mb_strcut($subject, 0, $matches[0][1], $this->encoding), $this->encoding);
        } else {
            $offset = $matches[0][1];
        }

        // [0][0] contains the matched text
        // [0][1] contains the index of that match
        $this->advanceBy($offset + mb_strlen($matches[0][0], $this->encoding));

        return $matches[0][0];
    }

    /**
     * Encapsulates the current state of this cursor in case you need to rollback later.
     *
     * WARNING: Do not parse or use the return value for ANYTHING except for
     * passing it back into restoreState(), as the number of values and their
     * contents may change in any future release without warning.
     *
     * @return array
     */
    public function saveState()
    {
        return [
            $this->currentPosition,
            $this->previousPosition,
            $this->nextNonSpaceCache,
            $this->indent,
            $this->column,
            $this->partiallyConsumedTab,
        ];
    }

    /**
     * Restore the cursor to a previous state.
     *
     * Pass in the value previously obtained by calling saveState().
     *
     * @param array $state
     */
    public function restoreState($state)
    {
        list(
            $this->currentPosition,
            $this->previousPosition,
            $this->nextNonSpaceCache,
            $this->indent,
            $this->column,
            $this->partiallyConsumedTab,
          ) = $state;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->currentPosition;
    }

    /**
     * @return string
     */
    public function getPreviousText()
    {
        return mb_substr($this->line, $this->previousPosition, $this->currentPosition - $this->previousPosition, $this->encoding);
    }

    /**
     * @return int
     */
    public function getColumn()
    {
        return $this->column;
    }
}
