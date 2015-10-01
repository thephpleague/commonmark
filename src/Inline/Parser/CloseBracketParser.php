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

namespace League\CommonMark\Inline\Parser;

use League\CommonMark\Cursor;
use League\CommonMark\Delimiter\Delimiter;
use League\CommonMark\Delimiter\DelimiterStack;
use League\CommonMark\Environment;
use League\CommonMark\EnvironmentAwareInterface;
use League\CommonMark\Inline\Element\AbstractWebResource;
use League\CommonMark\Inline\Element\Image;
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\InlineParserContext;
use League\CommonMark\Reference\Reference;
use League\CommonMark\Reference\ReferenceMap;
use League\CommonMark\Util\LinkParserHelper;

class CloseBracketParser extends AbstractInlineParser implements EnvironmentAwareInterface
{
    /**
     * @var Environment
     */
    protected $environment;

    /**
     * @return string[]
     */
    public function getCharacters()
    {
        return [']'];
    }

    /**
     * @param InlineParserContext $inlineContext
     *
     * @return bool
     */
    public function parse(InlineParserContext $inlineContext)
    {
        $cursor = $inlineContext->getCursor();

        $startPos = $cursor->getPosition();
        $previousState = $cursor->saveState();

        // Look through stack of delimiters for a [ or !
        $opener = $inlineContext->getDelimiterStack()->searchByCharacter(['[', '!']);
        if ($opener === null) {
            return false;
        }

        if (!$opener->isActive()) {
            // no matched opener; remove from emphasis stack
            $inlineContext->getDelimiterStack()->removeDelimiter($opener);

            return false;
        }

        $isImage = $opener->getChar() === '!';

        $cursor->advance();

        // Check to see if we have a link/image
        if (!($link = $this->tryParseLink($cursor, $inlineContext->getReferenceMap(), $opener, $startPos))) {
            // No match
            $inlineContext->getDelimiterStack()->removeDelimiter($opener); // Remove this opener from stack
            $cursor->restoreState($previousState);

            return false;
        }

        $inline = $this->createInline($link['url'], $link['title'], $isImage);
        $opener->getInlineNode()->replaceWith($inline);
        while (($label = $inline->next()) !== null) {
            $inline->appendChild($label);
        }

        $delimiterStack = $inlineContext->getDelimiterStack();
        $stackBottom = $opener->getPrevious();
        foreach ($this->environment->getInlineProcessors() as $inlineProcessor) {
            $inlineProcessor->processInlines($delimiterStack, $stackBottom);
        }
        if ($delimiterStack instanceof DelimiterStack) {
            $delimiterStack->removeAll($stackBottom);
        }

        // processEmphasis will remove this and later delimiters.
        // Now, for a link, we also remove earlier link openers (no links in links)
        if (!$isImage) {
            $inlineContext->getDelimiterStack()->removeEarlierMatches('[');
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

    /**
     * @param Cursor       $cursor
     * @param ReferenceMap $referenceMap
     * @param Delimiter    $opener
     * @param int          $startPos
     *
     * @return array|bool
     */
    protected function tryParseLink(Cursor $cursor, ReferenceMap $referenceMap, Delimiter $opener, $startPos)
    {
        // Check to see if we have a link/image
        // Inline link?
        if ($cursor->getCharacter() === '(') {
            if ($result = $this->tryParseInlineLinkAndTitle($cursor)) {
                return $result;
            }
        } elseif ($link = $this->tryParseReference($cursor, $referenceMap, $opener, $startPos)) {
            return ['url' => $link->getDestination(), 'title' => $link->getTitle()];
        }

        return false;
    }

    /**
     * @param Cursor $cursor
     *
     * @return array|bool
     */
    protected function tryParseInlineLinkAndTitle(Cursor $cursor)
    {
        $cursor->advance();
        $cursor->advanceToFirstNonSpace();
        if (($dest = LinkParserHelper::parseLinkDestination($cursor)) === null) {
            return false;
        }

        $cursor->advanceToFirstNonSpace();

        $title = null;
        // make sure there's a space before the title:
        if (preg_match('/^\\s/', $cursor->peek(-1))) {
            $title = LinkParserHelper::parseLinkTitle($cursor) ?: '';
        }

        $cursor->advanceToFirstNonSpace();

        if ($cursor->match('/^\\)/') === null) {
            return false;
        }

        return ['url' => $dest, 'title' => $title];
    }

    /**
     * @param Cursor       $cursor
     * @param ReferenceMap $referenceMap
     * @param Delimiter    $opener
     * @param int          $startPos
     *
     * @return Reference|null
     */
    protected function tryParseReference(Cursor $cursor, ReferenceMap $referenceMap, Delimiter $opener, $startPos)
    {
        $savePos = $cursor->saveState();
        $cursor->advanceToFirstNonSpace();
        $beforeLabel = $cursor->getPosition();
        $n = LinkParserHelper::parseLinkLabel($cursor);
        if ($n === 0 || $n === 2) {
            // Empty or missing second label
            $reflabel = mb_substr($cursor->getLine(), $opener->getIndex(), $startPos - $opener->getIndex(), 'utf-8');
        } else {
            $reflabel = mb_substr($cursor->getLine(), $beforeLabel + 1, $n - 2, 'utf-8');
        }

        if ($n === 0) {
            // If shortcut reference link, rewind before spaces we skipped
            $cursor->restoreState($savePos);
        }

        return $referenceMap->getReference($reflabel);
    }

    /**
     * @param string $url
     * @param string $title
     * @param bool   $isImage
     *
     * @return AbstractWebResource
     */
    protected function createInline($url, $title, $isImage)
    {
        if ($isImage) {
            return new Image($url, null, $title);
        } else {
            return new Link($url, null, $title);
        }
    }
}
