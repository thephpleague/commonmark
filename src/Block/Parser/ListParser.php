<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Block\Parser;

use League\CommonMark\Block\Element\ListBlock;
use League\CommonMark\Block\Element\ListData;
use League\CommonMark\Block\Element\ListItem;
use League\CommonMark\Block\Element\Paragraph;
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
        $tmpCursor->advanceToNextNonSpaceOrTab();
        $rest = $tmpCursor->getRemainder();

        $data = new ListData();
        $data->markerOffset = $cursor->getIndent();

        if (preg_match('/^[*+-]/', $rest) === 1) {
            $data->type = ListBlock::TYPE_UNORDERED;
            $data->delimiter = null;
            $data->bulletChar = $rest[0];
            $markerLength = 1;
        } elseif (($matches = RegexHelper::matchAll('/^(\d{1,9})([.)])/', $rest)) && (!($context->getContainer() instanceof Paragraph) || $matches[1] === '1')) {
            $data->type = ListBlock::TYPE_ORDERED;
            $data->start = (int) $matches[1];
            $data->delimiter = $matches[2];
            $data->bulletChar = null;
            $markerLength = strlen($matches[0]);
        } else {
            return false;
        }

        // Make sure we have spaces after
        $nextChar = $tmpCursor->peek($markerLength);
        if (!($nextChar === null || $nextChar === "\t" || $nextChar === ' ')) {
            return false;
        }

        // If it interrupts paragraph, make sure first line isn't blank
        $container = $context->getContainer();
        if ($container instanceof Paragraph && !RegexHelper::matchAt(RegexHelper::REGEX_NON_SPACE, $rest, $markerLength)) {
            return false;
        }

        // We've got a match! Advance offset and calculate padding
        $cursor->advanceToNextNonSpaceOrTab(); // to start of marker
        $cursor->advanceBy($markerLength, true); // to end of marker
        $data->padding = $this->calculateListMarkerPadding($cursor, $markerLength);

        // add the list if needed
        if (!$container || !($container instanceof ListBlock) || !$data->equals($container->getListData())) {
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

        while ($cursor->getColumn() - $spacesStartCol < 5) {
            if (!$cursor->advanceBySpaceOrTab()) {
                break;
            }
        }

        $blankItem = $cursor->peek() === null;
        $spacesAfterMarker = $cursor->getColumn() - $spacesStartCol;

        if ($spacesAfterMarker >= 5 || $spacesAfterMarker < 1 || $blankItem) {
            $cursor->restoreState($start);
            $cursor->advanceBySpaceOrTab();

            return $markerLength + 1;
        }

        return $markerLength + $spacesAfterMarker;
    }
}
