<?php

declare(strict_types=1);

/*
 * This is part of the webuni/commonmark-table-extension package.
 *
 * (c) Martin Hasoň <martin.hason@gmail.com>
 * (c) Webuni s.r.o. <info@webuni.cz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webuni\CommonMark\TableExtension;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Element\AbstractStringContainerBlock;
use League\CommonMark\Block\Element\InlineContainerInterface;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Node\Node;

class TableCaption extends AbstractStringContainerBlock implements InlineContainerInterface
{
    public $id;

    public function __construct(string $caption, string $id = null)
    {
        parent::__construct();
        $this->finalStringContents = $caption;
        $this->id = $id;
    }

    public function canContain(AbstractBlock $block): bool
    {
        return false;
    }

    public function acceptsLines(): bool
    {
        return false;
    }

    public function isCode(): bool
    {
        return false;
    }

    public function matchesNextLine(Cursor $cursor): bool
    {
        return false;
    }

    /**
     * @return AbstractInline[]
     */
    public function children(): array
    {
        return array_filter(parent::children(), function (Node $child): bool { return $child instanceof AbstractInline; });
    }

    public function handleRemainingContents(ContextInterface $context, Cursor $cursor): void
    {
    }
}
