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

use League\CommonMark\Util\RegexHelper;

class Cursor
{
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
    private $previousPosition = 0;

    /**
     * @var int|null
     */
    private $firstNonSpaceCache;

    /**
     * @param string $line
     */
    public function __construct($line)
    {
        $this->line = $line;
        $this->length = strlen($line);
    }

    /**
     * Returns the position of the next non-space character
     *
     * @return int
     */
    public function getFirstNonSpacePosition()
    {
        if ($this->firstNonSpaceCache === null) {
            $match = RegexHelper::matchAt('/[^ ]/', $this->line, $this->currentPosition);
            $this->firstNonSpaceCache = ($match === null) ? $this->length : $match;
        }

        return $this->firstNonSpaceCache;
    }

    /**
     * Returns the next character which isn't a space
     *
     * @return string
     */
    public function getFirstNonSpaceCharacter()
    {
        return $this->getCharacter($this->getFirstNonSpacePosition());
    }

    /**
     * Calculates the current indent (number of spaces after current position)
     *
     * @return int
     */
    public function getIndent()
    {
        return $this->getFirstNonSpacePosition() - $this->currentPosition;
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
            return null;
        }

        return $this->line[$index];
    }

    /**
     * Returns the next character (or null, if none) without advancing forwards
     *
     * @param int $offset
     *
     * @return null
     */
    public function peek($offset = 1)
    {
        if (!isset($this->line[$this->currentPosition + $offset])) {
            return null;
        }

        return $this->line[$this->currentPosition + $offset];
    }

    /**
     * Safely changes the current position
     *
     * @param int $newPosition
     */
    private function setCurrentPosition($newPosition)
    {
        if ($newPosition === $this->currentPosition) {
            return;
        }

        $this->previousPosition = $this->currentPosition;

        if ($newPosition >= $this->length) {
            $this->currentPosition = $this->length;
        } else {
            $this->currentPosition = $newPosition;
        }

        // Clear the cached value
        $this->firstNonSpaceCache = null;
    }

    /**
     * Whether the remainder is blank
     *
     * @return bool
     */
    public function isBlank()
    {
        return $this->getFirstNonSpacePosition() === $this->length;
    }

    /**
     * Move the cursor forwards
     *
     * @param int|null $characters
     *   Optional number of characters (defaults to 1)
     *
     * @return int
     *   Number of positions moved
     */
    public function advance($characters = null)
    {
        if ($characters === 0) {
            return 0;
        }

        if ($characters === null) {
            $characters = 1;
        }

        $this->setCurrentPosition($this->currentPosition + $characters);

        return $this->currentPosition - $this->previousPosition;
    }

    /**
     * Advances the cursor while the given character is matched
     *
     * @param string $character
     *   Character to match
     * @param int|null $maximumCharactersToAdvance
     *   Maximum number of characters to advance before giving up
     *
     * @return int
     *   Number of positions moved (0 if unsuccessful)
     */
    public function advanceWhileMatches($character, $maximumCharactersToAdvance = null)
    {
        // Calculate how far to advance
        $start = $this->currentPosition;
        $newIndex = $start;
        if ($maximumCharactersToAdvance === null) {
            $maximumCharactersToAdvance = $this->length;
        }

        $max = min($start + $maximumCharactersToAdvance, $this->length);

        while ($newIndex < $max && $this->line[$newIndex] === $character) {
            ++$newIndex;
        }

        if ($newIndex > $start) {
            return $this->advance($newIndex - $start);
        } else {
            return 0;
        }
    }

    /**
     * Parse zero or more space characters, including at most one newline
     *
     * @return int
     *   Number of positions moved
     */
    public function advanceToFirstNonSpace()
    {
        $matches = array();
        if (!preg_match('/^ *(?:\n *)?/', $this->getRemainder(), $matches, PREG_OFFSET_CAPTURE)) {
            return 0;
        }

        // [0][0] contains the matched text
        // [0][1] contains the index of that match
        $increment = $matches[0][1] + strlen($matches[0][0]);

        return $this->advance($increment);
    }

    /**
     * @return string
     */
    public function getRemainder()
    {
        if ($this->isAtEnd()) {
            return '';
        } else {
            return substr($this->line, $this->currentPosition);
        }
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
        $matches = array();
        if (!preg_match($regex, $this->getRemainder(), $matches, PREG_OFFSET_CAPTURE)) {
            return null;
        }

        // [0][0] contains the matched text
        // [0][1] contains the index of that match
        $this->advance($matches[0][1] + strlen($matches[0][0]));

        return $matches[0][0];
    }

    /**
     * @return CursorState
     */
    public function saveState()
    {
        return new CursorState($this->line, $this->length, $this->currentPosition, $this->previousPosition, $this->firstNonSpaceCache);
    }

    /**
     * @param CursorState $state
     */
    public function restoreState(CursorState $state)
    {
        $this->line = $state->getLine();
        $this->length = $state->getLength();
        $this->currentPosition = $state->getCurrentPosition();
        $this->previousPosition = $state->getPreviousPosition();
        $this->firstNonSpaceCache = $state->getFirstNonSpaceCache();
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
        return substr($this->line, $this->previousPosition, $this->currentPosition - $this->previousPosition);
    }
}
