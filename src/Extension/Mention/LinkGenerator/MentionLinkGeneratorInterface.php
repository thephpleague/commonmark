<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\Mention\LinkGenerator;

use League\CommonMark\Inline\Element\Link;

interface MentionLinkGeneratorInterface
{
    /**
     * @param string $symbol
     * @param string $handle
     *
     * @return Link|null
     */
    public function generateLink(string $symbol, string $handle): ?Link;
}
