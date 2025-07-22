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

namespace League\CommonMark\Extension\TableOfContents;

use League\CommonMark\Extension\TableOfContents\Node\TableOfContents;
use League\CommonMark\Extension\TableOfContents\Node\TableOfContentsWrapper;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Node\Block\Document;

interface TableOfContentsGeneratorInterface
{
    /**
     * If there is a table of contents, returns either a `TableOfContents` or
     * `TableOfContentsWrapper` node object.
     *
     * @psalm-return TableOfContents|TableOfContentsWrapper
     */
    public function generate(Document $document): ?AbstractBlock;
}
