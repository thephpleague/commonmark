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

namespace League\CommonMark;

use League\CommonMark\Inline\Element\Text;

class InlineParserEngine
{
    protected $environment;

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    public function parse(ContextInterface $context, Cursor $cursor)
    {
        $inlineParserContext = new InlineParserContext($cursor);
        while (($character = $cursor->getCharacter()) !== null) {
            if ($matchingParsers = $this->environment->getInlineParsersForCharacter($character)) {
                foreach ($matchingParsers as $parser) {
                    if ($parser->parse($context, $inlineParserContext)) {
                        continue 2;
                    }
                }
            }

            // We reach here if none of the parsers can handle the input
            // Attempt to match multiple non-special characters at once
            $text = $cursor->match($this->environment->getInlineParserCharacterRegex());
            // This might fail if we're currently at a special character which wasn't parsed; if so, just add that character
            if ($text === null) {
                $cursor->advance();
                $text = $character;
            }

            $lastInline = $inlineParserContext->getInlines()->last();
            if ($lastInline instanceof Text && !isset($lastInline->data['delim'])) {
                $lastInline->append($text);
            } else {
                $inlineParserContext->getInlines()->add(new Text($text));
            }
        }

        foreach ($this->environment->getInlineProcessors() as $inlineProcessor) {
            $inlineProcessor->processInlines($inlineParserContext->getInlines(), $inlineParserContext->getDelimiterStack());
        }

        return $inlineParserContext->getInlines();
    }
}
