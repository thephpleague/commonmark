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

use League\CommonMark\Block\Element\Paragraph;
use League\CommonMark\Block\Parser\AbstractBlockParser;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;
use League\CommonMark\Util\RegexHelper;

class TableParser extends AbstractBlockParser
{
    const REGEXP_DEFINITION = '/(?: *(:?) *-+ *(:?) *)+(?=\||$)/';
    const REGEXP_CELLS = '/(?:`[^`]*`|\\\\\\\\|\\\\\||[^|`\\\\]+)+(?=\||$)/';
    const REGEXP_CAPTION = '/^\[(.+?)\](?:\[(.+)\])?\s*$/';

    public function parse(ContextInterface $context, Cursor $cursor)
    {
        $container = $context->getContainer();

        if (!$container instanceof Paragraph) {
            return false;
        }

        $lines = $container->getStrings();
        if (count($lines) < 1) {
            return false;
        }

        $match = RegexHelper::matchAll(self::REGEXP_DEFINITION, $cursor->getLine(), $cursor->getFirstNonSpacePosition());
        if (null === $match) {
            return false;
        }

        $columns = $this->parseColumns($match);
        $head = $this->parseRow(trim(array_pop($lines)), $columns, TableCell::TYPE_HEAD);
        if (null === $head) {
            return false;
        }

        $table = new Table(function (Cursor $cursor) use (&$table, $columns) {
            $row = $this->parseRow($cursor->getLine(), $columns);
            if (null === $row) {
                if (null !== $table->getCaption()) {
                    return false;
                }

                if (null !== ($caption = $this->parseCaption($cursor->getLine()))) {
                    $table->setCaption($caption);

                    return true;
                }

                return false;
            }

            $table->getBody()->appendChild($row);

            return true;
        });

        $table->getHead()->appendChild($head);

        if (count($lines) >= 1) {
            $paragraph = new Paragraph();
            foreach ($lines as $line) {
                $paragraph->addLine($line);
            }

            $context->replaceContainerBlock($paragraph);
            $context->addBlock($table);
        } else {
            $context->replaceContainerBlock($table);
        }

        return true;
    }

    private function parseColumns(array $match)
    {
        $columns = [];
        foreach ((array) $match[0] as $i => $column) {
            if (isset($match[1][$i]) && $match[1][$i] && isset($match[2][$i]) && $match[2][$i]) {
                $columns[] = TableCell::ALIGN_CENTER;
            } elseif (isset($match[1][$i]) && $match[1][$i]) {
                $columns[] = TableCell::ALIGN_LEFT;
            } elseif (isset($match[2][$i]) && $match[2][$i]) {
                $columns[] = TableCell::ALIGN_RIGHT;
            } else {
                $columns[] = null;
            }
        }

        return $columns;
    }

    private function parseRow($line, array $columns, $type = TableCell::TYPE_BODY)
    {
        $cells = RegexHelper::matchAll(self::REGEXP_CELLS, $line);

        if (null === $cells || $line === $cells[0]) {
            return;
        }

        $row = new TableRow();
        foreach ((array) $cells[0] as $i => $cell) {
            $row->appendChild(new TableCell(trim($cell), $type, isset($columns[$i]) ? $columns[$i] : null));
        }

        for ($j = count($columns) - 1; $j > $i; --$j) {
            $row->appendChild(new TableCell('', $type, null));
        }

        return $row;
    }

    private function parseCaption($line)
    {
        $caption = RegexHelper::matchAll(self::REGEXP_CAPTION, $line);

        if (null === $caption) {
            return;
        }

        return new TableCaption($caption[1], $caption[2]);
    }
}
