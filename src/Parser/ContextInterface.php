<?php

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

namespace League\CommonMark\Parser;

use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Reference\ReferenceParser;

interface ContextInterface
{
    public function getDocument(): Document;

    public function getTip(): ?AbstractBlock;

    public function setTip(?AbstractBlock $block): void;

    public function getLineNumber(): int;

    public function getLine(): string;

    public function getBlockCloser(): UnmatchedBlockCloser;

    public function getContainer(): AbstractBlock;

    public function setContainer(AbstractBlock $container): void;

    public function addBlock(AbstractBlock $block): void;

    public function replaceContainerBlock(AbstractBlock $replacement): void;

    public function getBlocksParsed(): bool;

    public function setBlocksParsed(bool $bool): void;

    public function getReferenceParser(): ReferenceParser;
}
