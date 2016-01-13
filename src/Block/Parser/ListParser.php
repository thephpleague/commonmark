<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Block\Parser;

use League\CommonMark\Block\Element\ListBlock;
use League\CommonMark\Block\Element\ListData;
use League\CommonMark\Block\Element\ListItem;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;
use League\CommonMark\Util\RegexHelper;

class ListParser extends AbstractBlockParser
{
    /**
     * @param ContextInterface $context
     * @param Cursor           $cursor
     *
     * @return bool
     */
    public function parse(ContextInterface $context, Cursor $cursor)
    {
        if ($cursor->isIndented() && !($context->getContainer() instanceof ListBlock)) {
            return false;
        }

        $tmpCursor = clone $cursor;
        $indent = $tmpCursor->advanceToFirstNonSpace();
        $rest = $tmpCursor->getRemainder();

        $data = new ListData();
        $data->markerOffset = $cursor->getIndent();

        if ($matches = RegexHelper::matchAll('/^[*+-]/', $rest)) {
            $data->type = ListBlock::TYPE_UNORDERED;
            $data->delimiter = null;
            $data->bulletChar = $matches[0][0];
        } elseif ($matches = RegexHelper::matchAll('/^(\d{1,9})([.)])/', $rest)) {
            $data->type = ListBlock::TYPE_ORDERED;
            $data->start = intval($matches[1]);
            $data->delimiter = $matches[2];
            $data->bulletChar = null;
        } else {
            return false;
        }

        $markerLength = strlen($matches[0]);

        // Make sure we have spaces after
        $nextChar = $tmpCursor->peek($markerLength);
        if (!($nextChar === null || $nextChar === "\t" || $nextChar === ' ')) {
            return false;
        }

        // We've got a match! Advance offset and calculate padding
        $cursor->advanceToFirstNonSpace(); // to start of marker
        $cursor->advanceBy($markerLength, true); // to end of marker
        $data->padding = $this->calculateListMarkerPadding($cursor, $markerLength, $data);

        // add the list if needed
        $container = $context->getContainer();
        if (!$container || !($context->getContainer() instanceof ListBlock) || !$data->equals($container->getListData())) {
            $context->addBlock(new ListBlock($data));
        }

        // add the list item
        $context->addBlock(new ListItem($data));

        return true;
    }

    /**
     * @param Cursor $cursor
     * @param int    $markerLength
     *
     * @return int
     */
    private function calculateListMarkerPadding(Cursor $cursor, $markerLength)
    {
        $start = $cursor->saveState();
        $spacesStartCol = $cursor->getColumn();
        $spacesStartOffset = $cursor->getPosition();
        do {
            $cursor->advanceBy(1, true);
            $nextChar = $cursor->getCharacter();
        } while ($cursor->getColumn() - $spacesStartCol < 5 && ($nextChar === ' ' || $nextChar === "\t"));

        $blankItem = $cursor->peek() === null;
        $spacesAfterMarker = $cursor->getColumn() - $spacesStartCol;

        if ($spacesAfterMarker >= 5 || $spacesAfterMarker < 1 || $blankItem) {
            $cursor->restoreState($start);
            if ($cursor->peek() === ' ') {
                $cursor->advanceBy(1, true);
            }

            return $markerLength + 1;
        } else {
            return $markerLength + $spacesAfterMarker;
        }
    }
}
