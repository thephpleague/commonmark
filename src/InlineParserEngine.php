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

namespace League\CommonMark;

use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Node\Node;
use League\CommonMark\Reference\ReferenceMap;

class InlineParserEngine
{
    protected $environment;

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @param Node         $container
     * @param ReferenceMap $referenceMap
     */
    public function parse(Node $container, ReferenceMap $referenceMap)
    {
        $inlineParserContext = new InlineParserContext($container, $referenceMap);
        while (($character = $inlineParserContext->getCursor()->getCharacter()) !== null) {
            if (!$this->parseCharacter($character, $inlineParserContext)) {
                $this->addPlainText($character, $container, $inlineParserContext);
            }
        }

        $this->processInlines($inlineParserContext);

        $this->collapseAdjoiningTextElements($inlineParserContext);
    }

    /**
     * @param string              $character
     * @param InlineParserContext $inlineParserContext
     *
     * @return bool Whether we successfully parsed a character at that position
     */
    protected function parseCharacter($character, InlineParserContext $inlineParserContext)
    {
        foreach ($this->environment->getInlineParsersForCharacter($character) as $parser) {
            if ($parser->parse($inlineParserContext)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param InlineParserContext $inlineParserContext
     */
    protected function processInlines(InlineParserContext $inlineParserContext)
    {
        $delimiterStack = $inlineParserContext->getDelimiterStack();

        foreach ($this->environment->getInlineProcessors() as $inlineProcessor) {
            $inlineProcessor->processInlines($delimiterStack);
        }

        // Remove all delimiters
        $delimiterStack->removeAll();
    }

    /**
     * @param string              $character
     * @param Node                $container
     * @param InlineParserContext $inlineParserContext
     */
    private function addPlainText($character, Node $container, InlineParserContext $inlineParserContext)
    {
        // We reach here if none of the parsers can handle the input
        // Attempt to match multiple non-special characters at once
        $text = $inlineParserContext->getCursor()->match($this->environment->getInlineParserCharacterRegex());
        // This might fail if we're currently at a special character which wasn't parsed; if so, just add that character
        if ($text === null) {
            $inlineParserContext->getCursor()->advance();
            $text = $character;
        }

        $lastInline = $container->lastChild();
        if ($lastInline instanceof Text && !isset($lastInline->data['delim'])) {
            $lastInline->append($text);
        } else {
            $container->appendChild(new Text($text));
        }
    }

    private function collapseAdjoiningTextElements(InlineParserContext $context)
    {
        $walker = $context->getContainer()->walker();

        while (($event = $walker->next()) !== null) {
            if ($event->isEntering()) {
                $node = $event->getNode();
                if ($node instanceof Text) {
                    while (($next = $node->next()) && $next instanceof Text) {
                        $node->append($next->getContent());
                        $next->detach();
                    }
                }
            }
        }
    }
}
