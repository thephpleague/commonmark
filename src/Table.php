<?php

/*
 * This is part of the webuni/commonmark-table-extension package.
 *
 * (c) Martin Hasoň <martin.hason@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webuni\CommonMark\TableExtension;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;

class Table extends AbstractBlock
{
    private $head;
    private $body;
    private $parser;

    public function __construct(\Closure $parser)
    {
        parent::__construct();
        parent::addChild($this->head = new TableRows(TableRows::TYPE_HEAD));
        parent::addChild($this->body = new TableRows(TableRows::TYPE_BODY));
        $this->parser = $parser;
    }

    public function canContain(AbstractBlock $block)
    {
        return $block instanceof TableRows;
    }

    public function acceptsLines()
    {
        return true;
    }

    public function isCode()
    {
        return false;
    }

    public function addChild(AbstractBlock $childBlock)
    {
        throw new \OutOfRangeException('The Table block has a fixed number children');
    }

    public function removeChild(AbstractBlock $childBlock)
    {
        throw new \OutOfRangeException('The Table block has a fixed number children');
    }

    public function replaceChild(ContextInterface $context, AbstractBlock $original, AbstractBlock $replacement)
    {
        throw new \OutOfRangeException('The Table block has a fixed number children');
    }

    public function getHead()
    {
        return $this->head;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function matchesNextLine(Cursor $cursor)
    {
        return call_user_func($this->parser, $cursor);
    }

    public function handleRemainingContents(ContextInterface $context, Cursor $cursor)
    {
    }
}
