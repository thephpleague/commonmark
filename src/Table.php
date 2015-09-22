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
    private $parser;

    public function __construct(\Closure $parser)
    {
        parent::__construct();
        $this->appendChild(new TableRows(TableRows::TYPE_HEAD));
        $this->appendChild(new TableRows(TableRows::TYPE_BODY));
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

    public function getHead()
    {
        foreach ($this->children() as $child) {
            if ($child instanceof TableRows && $child->isHead()) {
                return $child;
            }
        }
    }

    public function getBody()
    {
        foreach ($this->children() as $child) {
            if ($child instanceof TableRows && $child->isBody()) {
                return $child;
            }
        }
    }

    public function matchesNextLine(Cursor $cursor)
    {
        return call_user_func($this->parser, $cursor);
    }

    public function handleRemainingContents(ContextInterface $context, Cursor $cursor)
    {
    }
}
