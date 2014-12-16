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

namespace League\CommonMark\Inline\Parser;

use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;
use League\CommonMark\Delimiter\Delimiter;
use League\CommonMark\Environment;
use League\CommonMark\EnvironmentAwareInterface;
use League\CommonMark\Inline\Element\AbstractWebResource;
use League\CommonMark\InlineParserContext;
use League\CommonMark\Inline\Element\Image;
use League\CommonMark\Inline\Element\InlineCollection;
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Reference\Reference;
use League\CommonMark\Reference\ReferenceMap;
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
        $cursor = $inlineContext->getCursor();

        $startPos = $cursor->getPosition();
        $previousState = $cursor->saveState();

        // Look through stack of delimiters for a [ or !
        $opener = $inlineContext->getDelimiterStack()->searchByCharacter(array('[', '!'));
        if ($opener === null) {
            return false;
        }

        // If we got here, open is a potential opener
        $isImage = $opener->getChar() === '!';
        // Instead of copying a slice, we null out the
        // parts of inlines that don't correspond to linkText;
        // later, we'll collapse them. This is awkways, and coul
        // be simplified if we made inlines a linked list rather than
        // and array:
        $linkTextInlines = new ArrayCollection($inlineContext->getInlines()->toArray());
        for ($i = 0; $i < $opener->getPos() + 1; $i++) {
            $linkTextInlines->set($i, null);
        }

        $cursor->advance();

        // Check to see if we have a link/image
        if (!($link = $this->tryParseLink($cursor, $context->getDocument()->getReferenceMap(), $opener, $startPos))) {
            // No match
            $inlineContext->getDelimiterStack()->removeDelimiter($opener); // Remove this opener from stack
            $cursor->restoreState($previousState);

            return false;
        }

        $dest = $link['dest'];
        $title = $link['title'];

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

        $inline = $this->createInline($dest, new InlineCollection($linkTextInlines), $title, $isImage);
        $inlineContext->getInlines()->add($inline);

        return true;
    }

    /**
     * @param Environment $environment
     */
    public function setEnvironment(Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @param Cursor $cursor
     * @param ReferenceMap $referenceMap
     * @param Delimiter $opener
     * @param int $startPos
     *
     * @return array|bool
     */
    private function tryParseLink(Cursor $cursor, ReferenceMap $referenceMap, Delimiter $opener, $startPos)
    {
        // Check to see if we have a link/image
        // Inline link?
        if ($cursor->getCharacter() == '(') {
            if ($result = $this->tryParseInlineLinkAndTitle($cursor)) {
                return $result;
            }
        } elseif ($link = $this->tryParseReference($cursor, $referenceMap, $opener, $startPos)) {
            return array('dest' => $link->getDestination(), 'title' => $link->getTitle());
        }

        return false;
    }

    /**
     * @param Cursor $cursor
     *
     * @return array|bool
     */
    private function tryParseInlineLinkAndTitle(Cursor $cursor)
    {
        $cursor->advance();
        $cursor->advanceToFirstNonSpace();
        if (($dest = LinkParserHelper::parseLinkDestination($cursor)) === null) {
            return false;
        }

        $cursor->advanceToFirstNonSpace();

        // make sure there's a space before the title:
        if (preg_match('/^\\s/', $cursor->peek(-1))) {
            $title = LinkParserHelper::parseLinkTitle($cursor) ?: '';
        } else {
            $title = null;
        }

        $cursor->advanceToFirstNonSpace();

        if (!$cursor->match('/^\\)/')) {
            return false;
        }

        return array('dest' => $dest, 'title' => $title);
    }

    /**
     * @param Cursor $cursor
     * @param ReferenceMap $referenceMap
     * @param Delimiter $opener
     * @param int $startPos
     *
     * @return Reference|null
     */
    private function tryParseReference(Cursor $cursor, ReferenceMap $referenceMap, Delimiter $opener, $startPos)
    {
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

        return $referenceMap->getReference($reflabel);
    }

    /**
     * @param string $url
     * @param InlineCollection $labelInlines
     * @param string $title
     * @param bool $isImage
     *
     * @return AbstractWebResource
     */
    private function createInline($url, InlineCollection $labelInlines, $title, $isImage)
    {
        if ($isImage) {
            return new Image($url, $labelInlines, $title);
        } else {
            return new Link($url, $labelInlines, $title);
        }
    }
}
