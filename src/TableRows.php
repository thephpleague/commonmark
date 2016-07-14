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
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;

class TableRows extends AbstractBlock
{
    const TYPE_HEAD = 'thead';
    const TYPE_BODY = 'tbody';

    public $type = self::TYPE_BODY;

    public function __construct($type = self::TYPE_BODY)
    {
        parent::__construct();
        $this->type = $type;
    }

    public function isHead()
    {
        return self::TYPE_HEAD === $this->type;
    }

    public function isBody()
    {
        return self::TYPE_BODY === $this->type;
    }

    public function canContain(AbstractBlock $block)
    {
        return $block instanceof TableRow;
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
