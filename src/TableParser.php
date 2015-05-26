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

use League\CommonMark\Block\Element\Paragraph;
use League\CommonMark\Block\Parser\AbstractBlockParser;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;
use League\CommonMark\Util\RegexHelper;

class TableParser extends AbstractBlockParser
{
    public function parse(ContextInterface $context, Cursor $cursor)
    {
        $container = $context->getContainer();
        if (!$container instanceof Paragraph) {
            return false;
        }

        $lines = $context->getContainer()->getStrings();
        if (count($lines) < 1) {
            return false;
        }

        $match = RegexHelper::matchAll(TableRow::REGEXP_DEFINITION, $cursor->getLine(), $cursor->getFirstNonSpacePosition());
        if ($match === null) {
            return false;
        }

        $columns = array();
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

        $table = new Table($columns);
        $table->addLine(trim(array_pop($lines)));

        if (count($lines) >= 1) {
            $paragraph = new Paragraph();
            foreach ($lines as $line) {
                $paragraph->addLine($line);
            }

            $context->replaceContainerBlock($paragraph);
            $container->addChild($table);
        } else {
            $context->replaceContainerBlock($table);
        }

        return true;
    }
}




















