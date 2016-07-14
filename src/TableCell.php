<?php

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
use League\CommonMark\Block\Element\InlineContainer;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;

class TableCell extends AbstractBlock implements InlineContainer
{
    const TYPE_HEAD = 'th';
    const TYPE_BODY = 'td';

    const ALIGN_LEFT = 'left';
    const ALIGN_RIGHT = 'right';
    const ALIGN_CENTER = 'center';

    public $type = self::TYPE_BODY;
    public $align;

    public function __construct($string = '', $type = self::TYPE_BODY, $align = null)
    {
        parent::__construct();
        $this->finalStringContents = $string;
        $this->type = $type;
        $this->align = $align;
    }

    public function canContain(AbstractBlock $block)
    {
        return false;
    }

    public function acceptsLines()
    {
        return false;
    }

    public function isCode()
    {
        return false;
    }

    public function matchesNextLine(Cursor $cursor)
    {
        return false;
    }

    public function handleRemainingContents(ContextInterface $context, Cursor $cursor)
    {
    }
}
