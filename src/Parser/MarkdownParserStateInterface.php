<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Parser;

use League\CommonMark\Parser\Block\BlockContinueParserInterface;

interface MarkdownParserStateInterface
{
    /**
     * Returns the deepest open block parser
     *
     * @return BlockContinueParserInterface
     */
    public function getActiveBlockParser(): BlockContinueParserInterface;

    /**
     * Open block parser that was last matched during the continue phase. This is different from the currently active
     * block parser, as an unmatched block is only closed when a new block is started.
     *
     * @return BlockContinueParserInterface
     */
    public function getLastMatchedBlockParser(): BlockContinueParserInterface;

    /**
     * Returns the current content of the paragraph if the matched block is a paragraph. The content can be multiple
     * lines separated by newlines.
     *
     * @return string|null
     */
    public function getParagraphContent(): ?string;
}
