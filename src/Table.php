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
        return $block instanceof TableRows || $block instanceof TableCaption;
    }

    public function acceptsLines()
    {
        return true;
    }

    public function isCode()
    {
        return false;
    }

    public function setCaption(TableCaption $caption = null)
    {
        $node = $this->getCaption();
        if ($node instanceof TableCaption) {
            $node->detach();
        }

        if ($caption instanceof TableCaption) {
            $this->prependChild($caption);
        }
    }

    public function getCaption()
    {
        foreach ($this->children() as $child) {
            if ($child instanceof TableCaption) {
                return $child;
            }
        }
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
