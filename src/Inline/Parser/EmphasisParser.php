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

use League\CommonMark\ContextInterface;
use League\CommonMark\Delimiter\Delimiter;
use League\CommonMark\InlineParserContext;
use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Util\RegexHelper;

class EmphasisParser extends AbstractInlineParser
{
    /**
     * @return string[]
     */
    public function getCharacters()
    {
        return ['*', '_'];
    }

    /**
     * @param ContextInterface $context
     * @param InlineParserContext $inlineContext
     *
     * @return bool
     */
    public function parse(ContextInterface $context, InlineParserContext $inlineContext)
    {
        $character = $inlineContext->getCursor()->getCharacter();
        if (!in_array($character, $this->getCharacters())) {
            return false;
        }

        $numDelims = 0;

        $cursor = $inlineContext->getCursor();
        $charBefore = $cursor->peek(-1);
        if ($charBefore === null) {
            $charBefore = "\n";
        }

        while ($cursor->peek($numDelims) === $character) {
            ++$numDelims;
        }

        $cursor->advanceBy($numDelims);

        $charAfter = $cursor->getCharacter();
        if ($charAfter === null) {
            $charAfter = "\n";
        }

        $afterIsWhitespace = preg_match('/\pZ|\s/u', $charAfter);
        $afterIsPunctuation = preg_match(RegexHelper::REGEX_PUNCTUATION, $charAfter);
        $beforeIsWhitespace = preg_match('/\pZ|\s/u', $charBefore);
        $beforeIsPunctuation = preg_match(RegexHelper::REGEX_PUNCTUATION, $charBefore);

        $leftFlanking = $numDelims > 0 && !$afterIsWhitespace &&
            !($afterIsPunctuation &&
            !$beforeIsWhitespace &&
            !$beforeIsPunctuation);

        $rightFlanking = $numDelims > 0 && !$beforeIsWhitespace &&
            !($beforeIsPunctuation &&
            !$afterIsWhitespace &&
            !$afterIsPunctuation);

        if ($character === '_') {
            $canOpen = $leftFlanking && (!$rightFlanking || $beforeIsPunctuation);
            $canClose = $rightFlanking && (!$leftFlanking || $afterIsPunctuation);
        } else {
            $canOpen = $leftFlanking;
            $canClose = $rightFlanking;
        }

        $inlineContext->getInlines()->add(
            new Text($cursor->getPreviousText(), ['delim' => true])
        );

        // Add entry to stack to this opener
        $delimiter = new Delimiter($character, $numDelims, $inlineContext->getInlines()->count() - 1, $canOpen, $canClose);
        $inlineContext->getDelimiterStack()->push($delimiter);

        return true;
    }
}
