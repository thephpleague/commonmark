<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * Additional emphasis processing code based on commonmark-java (https://github.com/atlassian/commonmark-java)
 *  - (c) Atlassian Pty Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Delimiter\Processor;

use League\CommonMark\Delimiter\Delimiter;
use League\CommonMark\Inline\Element\Text;

/**
 * Interface for a delimiter processor
 */
interface DelimiterProcessorInterface
{
    /**
     * Returns the character that marks the beginning of a delimited node.
     *
     * This must not clash with any other processors being added to the environment.
     *
     * @return string
     */
    public function getOpeningCharacter(): string;

    /**
     * Returns the character that marks the ending of a delimited node.
     *
     * This must not clash with any other processors being added to the environment.
     *
     * Note that for a symmetric delimiter such as "*", this is the same as the opening.
     *
     * @return string
     */
    public function getClosingCharacter(): string;

    /**
     * Minimum number of delimiter characters that are needed to active this.
     *
     * Must be at least 1.
     *
     * @return int
     */
    public function getMinLength(): int;

    /**
     * Determine how many (if any) of the delimiter characters should be used.
     *
     * This allows implementations to decide how many characters to be used
     * based on the properties of the delimiter runs. An implementation can also
     * return 0 when it doesn't want to allow this particular combination of
     * delimiter runs.
     *
     * @param Delimiter $opener The opening delimiter run
     * @param Delimiter $closer The closing delimiter run
     *
     * @return int
     */
    public function getDelimiterUse(Delimiter $opener, Delimiter $closer): int;

    /**
     * Process the matched delimiters, e.g. by wrapping the nodes between opener
     * and closer in a new node, or appending a new node after the opener.
     *
     * Note that removal of the delimiter from the delimiter nodes and detaching
     * them is done by the caller.
     *
     * @param Text $opener       The Text node that contained the opening delimiter
     * @param Text $closer       The text node that contained the closing delimiter
     * @param int  $delimiterUse The number of delimiters that were used
     */
    public function process(Text $opener, Text $closer, int $delimiterUse);
}
