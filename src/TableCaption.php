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
use League\CommonMark\Cursor;

class TableCaption extends AbstractBlock implements InlineContainer
{
    public $id;

    public function __construct($caption, $id = null)
    {
        parent::__construct();
        $this->finalStringContents = $caption;
        $this->id = $id;
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
}
