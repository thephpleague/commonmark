<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * Additional emphasis processing code based on commonmark-java (https://github.com/atlassian/commonmark-java)
 *  - (c) Atlassian Pty Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Delimiter;

use League\CommonMark\Delimiter\Processor\DelimiterProcessorCollection;
use League\CommonMark\Inline\AdjacentTextMerger;

class DelimiterStack
{
    /**
     * @var Delimiter|null
     */
    private $top;

    public function push(Delimiter $newDelimiter)
    {
        $newDelimiter->setPrevious($this->top);

        if ($this->top !== null) {
            $this->top->setNext($newDelimiter);
        }

        $this->top = $newDelimiter;
    }

    /**
     * @param Delimiter|null $stackBottom
     *
     * @return Delimiter|null
     */
    private function findEarliest(Delimiter $stackBottom = null): ?Delimiter
    {
        $delimiter = $this->top;
        while ($delimiter !== null && $delimiter->getPrevious() !== $stackBottom) {
            $delimiter = $delimiter->getPrevious();
        }

        return $delimiter;
    }

    /**
     * @param Delimiter $delimiter
     */
    public function removeDelimiter(Delimiter $delimiter)
    {
        if ($delimiter->getPrevious() !== null) {
            $delimiter->getPrevious()->setNext($delimiter->getNext());
        }

        if ($delimiter->getNext() === null) {
            // top of stack
            $this->top = $delimiter->getPrevious();
        } else {
            $delimiter->getNext()->setPrevious($delimiter->getPrevious());
        }
    }

    private function removeDelimiterAndNode(Delimiter $delimiter)
    {
        $delimiter->getInlineNode()->detach();
        $this->removeDelimiter($delimiter);
    }

    private function removeDelimitersBetween(Delimiter $opener, Delimiter $closer)
    {
        $delimiter = $closer->getPrevious();
        while ($delimiter !== null && $delimiter !== $opener) {
            $previous = $delimiter->getPrevious();
            $this->removeDelimiter($delimiter);
            $delimiter = $previous;
        }
    }

    /**
     * @param Delimiter|null $stackBottom
     */
    public function removeAll(Delimiter $stackBottom = null)
    {
        while ($this->top && $this->top !== $stackBottom) {
            $this->removeDelimiter($this->top);
        }
    }

    /**
     * @param string $character
     */
    public function removeEarlierMatches(string $character)
    {
        $opener = $this->top;
        while ($opener !== null) {
            if ($opener->getChar() === $character) {
                $opener->setActive(false);
            }

            $opener = $opener->getPrevious();
        }
    }

    /**
     * @param string|string[] $characters
     *
     * @return Delimiter|null
     */
    public function searchByCharacter($characters): ?Delimiter
    {
        if (!\is_array($characters)) {
            $characters = [$characters];
        }

        $opener = $this->top;
        while ($opener !== null) {
            if (\in_array($opener->getChar(), $characters)) {
                break;
            }
            $opener = $opener->getPrevious();
        }

        return $opener;
    }

    public function processDelimiters(?Delimiter $stackBottom, DelimiterProcessorCollection $processors)
    {
        $openersBottom = [];

        // Find first closer above stackBottom
        $closer = $this->findEarliest($stackBottom);

        // Move forward, looking for closers, and handling each
        while ($closer !== null) {
            $delimiterChar = $closer->getChar();

            $delimiterProcessor = $processors->getDelimiterProcessor($delimiterChar);
            if (!$closer->canClose() || $delimiterProcessor === null) {
                $closer = $closer->getNext();
                continue;
            }

            $openingDelimiterChar = $delimiterProcessor->getOpeningCharacter();

            $useDelims = 0;
            $openerFound = false;
            $potentialOpenerFound = false;
            $opener = $closer->getPrevious();
            while ($opener !== null && $opener !== $stackBottom && $opener !== ($openersBottom[$delimiterChar] ?? null)) {
                if ($opener->canOpen() && $opener->getChar() === $openingDelimiterChar) {
                    $potentialOpenerFound = true;
                    $useDelims = $delimiterProcessor->getDelimiterUse($opener, $closer);
                    if ($useDelims > 0) {
                        $openerFound = true;
                        break;
                    }
                }
                $opener = $opener->getPrevious();
            }

            if (!$openerFound) {
                if (!$potentialOpenerFound) {
                    // Only do this when we didn't even have a potential
                    // opener (one that matches the character and can open).
                    // If an opener was rejected because of the number of
                    // delimiters (e.g. because of the "multiple of 3"
                    // Set lower bound for future searches for openersrule),
                    // we want to consider it next time because the number
                    // of delimiters can change as we continue processing.
                    $openersBottom[$delimiterChar] = $closer->getPrevious();
                    if (!$closer->canOpen()) {
                        // We can remove a closer that can't be an opener,
                        // once we've seen there's no matching opener.
                        $this->removeDelimiter($closer);
                    }
                }
                $closer = $closer->getNext();
                continue;
            }

            $openerNode = $opener->getInlineNode();
            $closerNode = $closer->getInlineNode();

            // Remove number of used delimiters from stack and inline nodes.
            $opener->setNumDelims($opener->getNumDelims() - $useDelims);
            $closer->setNumDelims($closer->getNumDelims() - $useDelims);

            $openerNode->setContent(\substr($openerNode->getContent(), 0, -$useDelims));
            $closerNode->setContent(\substr($closerNode->getContent(), 0, -$useDelims));

            $this->removeDelimitersBetween($opener, $closer);
            // The delimiter processor can re-parent the nodes between opener and closer,
            // so make sure they're contiguous already. Exclusive because we want to keep opener/closer themselves.
            AdjacentTextMerger::mergeTextNodesBetweenExclusive($openerNode, $closerNode);
            $delimiterProcessor->process($openerNode, $closerNode, $useDelims);

            // No delimiter characters left to process, so we can remove delimiter and the now empty node.
            if ($opener->getNumDelims() === 0) {
                $this->removeDelimiterAndNode($opener);
            }

            if ($closer->getNumDelims() === 0) {
                $next = $closer->getNext();
                $this->removeDelimiterAndNode($closer);
                $closer = $next;
            }
        }

        // Remove all delimiters
        $this->removeAll($stackBottom);
    }
}
