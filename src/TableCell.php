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

class TableCell extends AbstractStringContainerBlock implements InlineContainerInterface
{
    const TYPE_HEAD = 'th';
    const TYPE_BODY = 'td';

    const ALIGN_LEFT = 'left';
    const ALIGN_RIGHT = 'right';
    const ALIGN_CENTER = 'center';

    public $type = self::TYPE_BODY;
    public $align;

    public function __construct(string $string = '', string $type = self::TYPE_BODY, string $align = null)
    {
        parent::__construct();
        $this->finalStringContents = $string;
        $this->type = $type;
        $this->align = $align;
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

    public function handleRemainingContents(ContextInterface $context, Cursor $cursor): void
    {
    }

    /**
     * @return AbstractInline[]
     */
    public function children(): array
    {
        return array_filter(parent::children(), function (Node $child): bool { return $child instanceof AbstractInline; });
    }

}
