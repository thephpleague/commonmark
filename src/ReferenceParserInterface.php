<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmarkjs)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark;

use League\CommonMark\Reference\Reference;
use League\CommonMark\Reference\ReferenceMap;
use League\CommonMark\Util\LinkParserHelper;

interface ReferenceParserInterface
{
    public function setReferenceMap(ReferenceMap $referenceMap);

    /**
     * Attempt to parse a link reference, modifying the refmap.
     *
     * @param Cursor $cursor
     *
     * @return bool
     */
    public function parse(Cursor $cursor);

    /**
     * @return string
     */
    public function getPrefix();
}
