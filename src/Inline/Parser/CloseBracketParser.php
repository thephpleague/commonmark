<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on stmd.js
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Inline\Parser;

use League\CommonMark\ContextInterface;
use League\CommonMark\Inline\Element\Image;
use League\CommonMark\Inline\Element\InlineCollection;
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Environment;
use League\CommonMark\EnvironmentAwareInterface;
use League\CommonMark\InlineParserContext;
use League\CommonMark\Util\ArrayCollection;
use League\CommonMark\Util\LinkParserHelper;

class CloseBracketParser extends AbstractInlineParser implements EnvironmentAwareInterface
{
    /**
     * @var Environment
     */
    private $environment;

    /**
     * @return string[]
     */
    public function getCharacters()
    {
        return array(']');
    }

    /**
     * @param ContextInterface $context
     * @param InlineParserContext $inlineContext
     *
     * @return bool
     */
    public function parse(ContextInterface $context, InlineParserContext $inlineContext)
    {
        $matched = false;

        $cursor = $inlineContext->getCursor();

        $startPos = $cursor->getPosition();

        $previousState = $cursor->saveState();

        $cursor->advance();

        // Look through stack of delimiters for a [ or !
        $opener = $inlineContext->getDelimiterStack()->searchByCharacter(array('[', '!'));
        if ($opener === null) {
            // No matched opener, just return a literal
            $inlineContext->getInlines()->add(new Text(']'));

            return true;
        }

        // If we got here, open is a potential opener
        $isImage = $opener->getChar() === '!';
        // Instead of copying a slice, we null out the
        // parts of inlines that don't correspond to linkText;
        // later, we'll collapse them. This is awkways, and coul
        // be simplified if we made inlines a linked list rather than
        // and array:
        $linkTextInlines = $inlineContext->getInlines()->slice(0);
        for ($i = 0; $i < $opener->getPos() + 1; $i++) {
            $linkTextInlines[$i] = null;
        }
        $linkTextInlines = new ArrayCollection($linkTextInlines);
        // Check to see if we have a link/image
        // Inline link?
        if ($cursor->getCharacter() == '(') {
            $cursor->advance();
            if ($cursor->advanceToFirstNonSpace() &&
                (($dest = LinkParserHelper::parseLinkDestination($cursor)) !== null) &&
                $cursor->advanceToFirstNonSpace()
            ) {
                // make sure there's a space before the title:
                if (preg_match('/^\\s/', $cursor->peek(-1))) {
                    $title = LinkParserHelper::parseLinkTitle($cursor) ?: '';
                } else {
                    $title = null;
                }
                if ($cursor->advanceToFirstNonSpace() && $cursor->match('/^\\)/')) {
                    $matched = true;
                }
            }
        } else {
            // Next, see if there's a link label
            $savePos = $cursor->saveState();
            $cursor->advanceToFirstNonSpace();
            $beforeLabel = $cursor->getPosition();
            $n = LinkParserHelper::parseLinkLabel($cursor);
            if ($n === 0 || $n === 2) {
                // Empty or missing second label
                $reflabel = substr($cursor->getLine(), $opener->getIndex(), $startPos - $opener->getIndex());
            } else {
                $reflabel = substr($cursor->getLine(), $beforeLabel + 1, $n - 2);
            }
            if ($n === 0) {
                // If shortcut reference link, rewind before spaces we skipped
                $cursor->restoreState($savePos);
            }
            // Lookup rawlabel in refmap
            if ($link = $context->getDocument()->getReferenceMap()->getReference($reflabel)) {
                $dest = $link->getDestination();
                $title = $link->getTitle();
                $matched = true;
            }
        }

        if (!$matched) { // No match
            $inlineContext->getDelimiterStack()->removeDelimiter($opener); // Remove this opener from stack
            $cursor->restoreState($previousState);

            return false;
        }

        foreach ($this->environment->getInlineProcessors() as $inlineProcessor) {
            $inlineProcessor->processInlines($linkTextInlines, $inlineContext->getDelimiterStack(), $opener->getPrevious());
        }

        // Remove the part of inlines that become link_text
        // See noter above on why we need to do this instead of splice:
        for ($i = $opener->getPos(); $i < $inlineContext->getInlines()->count(); $i++) {
            $inlineContext->getInlines()->set($i, null);
        }
        // processEmphasis will remove this and later delimiters.
        // Now, for a link, we also remove earlier link openers
        // (no links in links)
        if (!$isImage) {
            $inlineContext->getDelimiterStack()->removeEarlierMatches('[');
        }

        if ($isImage) {
            $inlineContext->getInlines()->add(new Image($dest, new InlineCollection($linkTextInlines), $title));
        } else {
            $inlineContext->getInlines()->add(new Link($dest, new InlineCollection($linkTextInlines), $title));
        }

        return true;
    }

    /**
     * @param Environment $environment
     */
    public function setEnvironment(Environment $environment)
    {
        $this->environment = $environment;
    }
}
