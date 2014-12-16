<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmarkjs)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Element\AbstractInlineContainer;
use League\CommonMark\Block\Element\Document;
use League\CommonMark\Block\Element\ListBlock;
use League\CommonMark\Block\Element\Paragraph;
use League\CommonMark\Util\TextHelper;

class DocParser
{
    /**
     * @var Environment
     */
    protected $environment;

    /**
     * @var InlineParserEngine
     */
    private $inlineParserEngine;

    /**
     * @param Environment $parsers
     */
    public function __construct(Environment $parsers)
    {
        $this->environment = $parsers;
        $this->inlineParserEngine = new InlineParserEngine($parsers);
    }

    /**
     * @return Environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @param string $input
     *
     * @return string[]
     */
    private function preProcessInput($input)
    {
        // Remove any /n which appears at the very end of the string
        if (substr($input, -1) == "\n") {
            $input = substr($input, 0, -1);
        }

        return preg_split('/\r\n|\n|\r/', $input);
    }

    /**
     * @param string $input
     *
     * @return Document
     */
    public function parse($input)
    {
        $context = new Context(new Document(), $this->getEnvironment());

        $lines = $this->preProcessInput($input);
        foreach ($lines as $line) {
            $context->setNextLine(TextHelper::detabLine($line));
            $this->incorporateLine($context);
        }

        while ($context->getTip()) {
            $context->getTip()->finalize($context);
        }

        $this->processInlines($context, $context->getDocument());

        return $context->getDocument();
    }

    private function incorporateLine(ContextInterface $context)
    {
        $cursor = new Cursor($context->getLine());
        $oldTip = $context->getTip();

        $context->setBlocksParsed(false);
        $context->setContainer($context->getDocument());
        while ($context->getContainer()->hasChildren()) {
            $lastChild = $context->getContainer()->getLastChild();
            if (!$lastChild->isOpen()) {
                break;
            }

            $context->setContainer($lastChild);
            if (!$context->getContainer()->matchesNextLine($cursor)) {
                $context->setContainer($context->getContainer()->getParent()); // back up to the last matching block
                break;
            }
        }

        $lastMatchedContainer = $context->getContainer();

        $context->setUnmatchedBlockCloser(function() use ($context, $oldTip, $lastMatchedContainer) {
            while ($oldTip !== $lastMatchedContainer) {
                $oldTip->finalize($context);
                $oldTip = $oldTip->getParent();
            }
        });

        // Check to see if we've hit 2nd blank line; if so break out of list:
        if ($cursor->isBlank() && $context->getContainer()->endsWithBlankLine()) {
            $this->breakOutOfLists($context, $context->getContainer());
        }

        while (!$context->getContainer()->isCode() && !$context->getBlocksParsed()) {
            $parsed = false;
            foreach ($this->environment->getBlockParsers() as $parser) {
                if ($parser->parse($context, $cursor)) {
                    $parsed = true;
                    break;
                }
            }

            if (!$parsed || $context->getContainer()->acceptsLines()) {
                $context->setBlocksParsed(true);
            }
        }

        // What remains at the offset is a text line.  Add the text to the appropriate container.
        // First check for a lazy paragraph continuation:
        if ($context->getTip() !== $lastMatchedContainer &&
            !$cursor->isBlank() &&
            $context->getTip() instanceof Paragraph &&
            count($context->getTip()->getStrings()) > 0
        ) {
            // lazy paragraph continuation
            $context->getTip()->addLine($cursor->getRemainder());
        } else { // not a lazy continuation
            // finalize any blocks not matched
            $context->closeUnmatchedBlocks();

            // Determine whether the last line is blank, updating parents as needed
            $context->getContainer()->setLastLineBlank($cursor, $context->getLineNumber());

            // Handle any remaining cursor contents
            $context->getContainer()->handleRemainingContents($context, $cursor);
        }
    }

    private function processInlines(ContextInterface $context, AbstractBlock $block)
    {
        if ($block instanceof AbstractInlineContainer) {
            $cursor = new Cursor(trim($block->getStringContent()));
            $block->setInlines($this->inlineParserEngine->parse($context, $cursor));
        }

        foreach ($block->getChildren() as $child) {
            $this->processInlines($context, $child);
        }
    }

    /**
     * Break out of all containing lists, resetting the tip of the
     * document to the parent of the highest list, and finalizing
     * all the lists.  (This is used to implement the "two blank lines
     * break of of all lists" feature.)
     *
     * @param ContextInterface $context
     * @param AbstractBlock $block
     */
    private function breakOutOfLists(ContextInterface $context, AbstractBlock $block)
    {
        $b = $block;
        $lastList = null;
        do {
            if ($b instanceof ListBlock) {
                $lastList = $b;
            }
            $b = $b->getParent();
        } while ($b);

        if ($lastList) {
            while ($block !== $lastList) {
                $block->finalize($context);
                $block = $block->getParent();
            }
            $lastList->finalize($context);
        }
    }
}
