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
use League\CommonMark\Util\ArrayCollection;
use League\CommonMark\Util\RegexHelper;

class Table extends AbstractBlock
{
    private $head;
    private $body;
    private $columns;

    public function __construct($columns = array())
    {
        parent::__construct();

        $this->columns = $columns;
        parent::addChild($this->head = new TableRows(TableRows::TYPE_HEAD));
        parent::addChild($this->body = new TableRows(TableRows::TYPE_BODY));
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
        return !$cursor->isBlank() && RegexHelper::matchAll(TableRow::REGEXP_CELLS, $cursor->getLine());
    }

    public function finalize(ContextInterface $context)
    {
        parent::finalize($context);

        $part = $this->head;
        foreach ($this->getStrings() as $line)  {
            if (RegexHelper::matchAll(TableRow::REGEXP_DEFINITION, $line)) {
                $part = $this->body;
                continue;
            }

            $part->addChild($row = new TableRow());
            $columns = RegexHelper::matchAll(TableRow::REGEXP_CELLS, $line);
            foreach ($columns[0] as $i => $column) {
                $row->addChild(new TableCell(
                    trim($column),
                    TableRows::TYPE_HEAD === $part->type ? TableCell::TYPE_HEAD : TableCell::TYPE_BODY,
                    isset($this->columns[$i]) ? $this->columns[$i] : null
                ));
            }

            if ($i >= count($this->columns) - 1) {
                continue;
            }

            for ($j = count($this->columns) - 1; $j > $i; $j--) {
                $row->addChild(new TableCell('', TableRows::TYPE_HEAD === $part->type ? TableCell::TYPE_HEAD : TableCell::TYPE_BODY), null);
            }
        }

        $this->strings = new ArrayCollection();
    }

    public function handleRemainingContents(ContextInterface $context, Cursor $cursor)
    {
        $cursor->advanceToFirstNonSpace();
        $this->addLine(trim($cursor->getRemainder()));
    }
}
