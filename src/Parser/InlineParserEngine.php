<?php

declare(strict_types=1);

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

namespace League\CommonMark\Parser;

use League\CommonMark\Delimiter\Delimiter;
use League\CommonMark\Delimiter\Processor\DelimiterProcessorInterface;
use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Node\Inline\AdjacentTextMerger;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Node\Node;
use League\CommonMark\Reference\ReferenceMapInterface;
use League\CommonMark\Util\RegexHelper;

/**
 * @internal
 */
final class InlineParserEngine implements InlineParserEngineInterface
{
    /**
     * @var EnvironmentInterface
     *
     * @psalm-readonly
     */
    private $environment;

    /**
     * @var ReferenceMapInterface
     *
     * @psalm-readonly
     */
    private $referenceMap;

    public function __construct(EnvironmentInterface $environment, ReferenceMapInterface $referenceMap)
    {
        $this->environment  = $environment;
        $this->referenceMap = $referenceMap;
    }

    public function parse(string $contents, AbstractBlock $block): void
    {
        $inlineParserContext = new InlineParserContext($contents, $block, $this->referenceMap);
        $cursor              = $inlineParserContext->getCursor();
        while (($character = $cursor->getCharacter()) !== null) {
            if (! $this->parseCharacter($character, $inlineParserContext)) {
                $this->addPlainText($character, $block, $inlineParserContext);
            }
        }

        $delimiterStack = $inlineParserContext->getDelimiterStack();
        $delimiterStack->processDelimiters(null, $this->environment->getDelimiterProcessors());
        $delimiterStack->removeAll();

        AdjacentTextMerger::mergeChildNodes($block);
    }

    /**
     * @return bool Whether we successfully parsed a character at that position
     */
    private function parseCharacter(string $character, InlineParserContext $inlineParserContext): bool
    {
        foreach ($this->environment->getInlineParsersForCharacter($character) as $parser) {
            if ($parser->parse($inlineParserContext)) {
                return true;
            }
        }

        if ($delimiterProcessor = $this->environment->getDelimiterProcessors()->getDelimiterProcessor($character)) {
            return $this->parseDelimiters($delimiterProcessor, $inlineParserContext);
        }

        return false;
    }

    private function parseDelimiters(DelimiterProcessorInterface $delimiterProcessor, InlineParserContext $inlineContext): bool
    {
        $cursor    = $inlineContext->getCursor();
        $character = $cursor->getCharacter();
        $numDelims = 0;

        if ($character === null) {
            throw new \RuntimeException('Cannot parse delimiters without a valid character');
        }

        $charBefore = $cursor->peek(-1);
        if ($charBefore === null) {
            $charBefore = "\n";
        }

        while ($cursor->peek($numDelims) === $character) {
            ++$numDelims;
        }

        if ($numDelims < $delimiterProcessor->getMinLength()) {
            return false;
        }

        $cursor->advanceBy($numDelims);

        $charAfter = $cursor->getCharacter();
        if ($charAfter === null) {
            $charAfter = "\n";
        }

        [$canOpen, $canClose] = self::determineCanOpenOrClose($charBefore, $charAfter, $character, $delimiterProcessor);

        $node = new Text(\str_repeat($character, $numDelims), [
            'delim' => true,
        ]);
        $inlineContext->getContainer()->appendChild($node);

        // Add entry to stack to this opener
        if ($canOpen || $canClose) {
            $delimiter = new Delimiter($character, $numDelims, $node, $canOpen, $canClose);
            $inlineContext->getDelimiterStack()->push($delimiter);
        }

        return true;
    }

    private function addPlainText(string $character, Node $container, InlineParserContext $inlineParserContext): void
    {
        // We reach here if none of the parsers can handle the input
        // Attempt to match multiple non-special characters at once
        $text = $inlineParserContext->getCursor()->match($this->environment->getInlineParserCharacterRegex());
        // This might fail if we're currently at a special character which wasn't parsed; if so, just add that character
        if ($text === null) {
            $inlineParserContext->getCursor()->advanceBy(1);
            $text = $character;
        }

        $lastInline = $container->lastChild();
        if ($lastInline instanceof Text && ! isset($lastInline->data['delim'])) {
            $lastInline->append($text);
        } else {
            $container->appendChild(new Text($text));
        }
    }

    /**
     * @return bool[]
     */
    private static function determineCanOpenOrClose(string $charBefore, string $charAfter, string $character, DelimiterProcessorInterface $delimiterProcessor): array
    {
        $afterIsWhitespace   = \preg_match(RegexHelper::REGEX_UNICODE_WHITESPACE_CHAR, $charAfter);
        $afterIsPunctuation  = \preg_match(RegexHelper::REGEX_PUNCTUATION, $charAfter);
        $beforeIsWhitespace  = \preg_match(RegexHelper::REGEX_UNICODE_WHITESPACE_CHAR, $charBefore);
        $beforeIsPunctuation = \preg_match(RegexHelper::REGEX_PUNCTUATION, $charBefore);

        $leftFlanking  = ! $afterIsWhitespace && (! $afterIsPunctuation || $beforeIsWhitespace || $beforeIsPunctuation);
        $rightFlanking = ! $beforeIsWhitespace && (! $beforeIsPunctuation || $afterIsWhitespace || $afterIsPunctuation);

        if ($character === '_') {
            $canOpen  = $leftFlanking && (! $rightFlanking || $beforeIsPunctuation);
            $canClose = $rightFlanking && (! $leftFlanking || $afterIsPunctuation);
        } else {
            $canOpen  = $leftFlanking && $character === $delimiterProcessor->getOpeningCharacter();
            $canClose = $rightFlanking && $character === $delimiterProcessor->getClosingCharacter();
        }

        return [$canOpen, $canClose];
    }
}
