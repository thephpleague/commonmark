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

namespace League\CommonMark\Node\Inline;

final class Text extends AbstractStringContainer
{
    /**
     * Marks a potential delimiter so the inline parser keeps its text separate.
     * Custom inline parsers should set this property in place of data['delim'].
     */
    public bool $isDelimiter = false;

    public function append(string $literal): void
    {
        $this->literal .= $literal;
    }
}
