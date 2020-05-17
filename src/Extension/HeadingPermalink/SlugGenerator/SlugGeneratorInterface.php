<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\HeadingPermalink\SlugGenerator;

use League\CommonMark\Node\Node;

/**
 * Creates URL-friendly strings based on the inner contents of a node and its descendants
 */
interface SlugGeneratorInterface
{
    public function generateSlug(Node $node): string;
}
