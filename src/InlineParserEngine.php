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
            $res = null;
            if ($matchingParsers = $this->environment->getInlineParsersForCharacter($character)) {
                foreach ($matchingParsers as $parser) {
                    if ($res = $parser->parse($context, $inlineParserContext)) {
                        break;
                    }
                }
            }

            if (!$res) {
                $cursor->advance();
                $lastInline = $inlineParserContext->getInlines()->last();
                if ($lastInline instanceof Text && !isset($lastInline->data['delim'])) {
                    $lastInline->append($character);
                } else {
                    $inlineParserContext->getInlines()->add(new Text($character));
                }
            }
        }

        foreach ($this->environment->getInlineProcessors() as $inlineProcessor) {
            $inlineProcessor->processInlines($inlineParserContext->getInlines(), $inlineParserContext->getDelimiterStack());
        }

        return $inlineParserContext->getInlines();
    }
}
