<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Inline\Parser;

use League\CommonMark\InlineParserContext;

interface InlineParserInterface
{
    /**
     * Get the name of the parser
     *
     * Note that this must be unique with its block type.
     *
     * @return string
     */
    public function getName();

    /**
     * @return string[]
     */
    public function getCharacters();

    /**
     * @param InlineParserContext $inlineContext
     *
     * @return bool
     */
    public function parse(InlineParserContext $inlineContext);
}
