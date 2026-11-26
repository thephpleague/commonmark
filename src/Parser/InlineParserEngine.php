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

use League\CommonMark\Delimiter\DelimiterParser;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Exception\UnexpectedEncodingException;
use League\CommonMark\Extension\Autolink\EmailAutolinkParser;
use League\CommonMark\Extension\Autolink\UrlAutolinkParser;
use League\CommonMark\Extension\CommonMark\Parser\Inline as CoreInlineParser;
use League\CommonMark\Extension\TaskList\TaskListItemMarkerParser;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Node\Inline\AdjacentTextMerger;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\NewlineParser;
use League\CommonMark\Reference\ReferenceMapInterface;

/**
 * @internal
 */
final class InlineParserEngine implements InlineParserEngineInterface
{
    /** @psalm-readonly */
    private EnvironmentInterface $environment;

    /** @psalm-readonly */
    private ReferenceMapInterface $referenceMap;

    /**
     * @var array<int, InlineParserInterface|string|bool>
     * @psalm-var list<array{0: InlineParserInterface, 1: non-empty-string, 2: bool}>
     * @phpstan-var array<int, array{0: InlineParserInterface, 1: non-empty-string, 2: bool}>
     */
    private array $parsers = [];

    // Required syntax for the parsers recognized by hasKnownTrigger().
    private const INLINE_TRIGGER = '~[\n\\\\&<\[\]`*_@\~]|www\.|://~i';

    private bool $canSkipPlainText;

    public function __construct(EnvironmentInterface $environment, ReferenceMapInterface $referenceMap)
    {
        $this->environment      = $environment;
        $this->referenceMap     = $referenceMap;
        $this->canSkipPlainText = $environment instanceof Environment;

        foreach ($environment->getInlineParsers() as $parser) {
            \assert($parser instanceof InlineParserInterface);
            $regex = $parser->getMatchDefinition()->getRegex();

            if ($this->canSkipPlainText && ! self::hasKnownTrigger($parser)) {
                $this->canSkipPlainText = false;
            }

            $this->parsers[] = [$parser, $regex, \strlen($regex) !== \mb_strlen($regex, 'UTF-8')];
        }
    }

    public function parse(string $contents, AbstractBlock $block): void
    {
        $contents = \trim($contents);

        if ($this->canSkipPlainText && $block->firstChild() === null && \preg_match(self::INLINE_TRIGGER, $contents) === 0) {
            if (! \mb_check_encoding($contents, 'UTF-8')) {
                throw new UnexpectedEncodingException('Unexpected encoding - UTF-8 or ASCII was expected');
            }

            if ($contents !== '') {
                $block->appendChild(new Text($contents));
            }

            return;
        }

        $cursor = new Cursor($contents);

        $inlineParserContext = new InlineParserContext($cursor, $block, $this->referenceMap, $this->environment->getConfiguration()->get('max_delimiters_per_line'));

        // Have all parsers look at the line to determine what they might want to parse and what positions they exist at
        $slotCount    = \max(1, \count($this->parsers) * 2);
        $lastPosition = -1;
        $parsed       = true;
        foreach ($this->matchParsers($contents, $slotCount) as $key => $matches) {
            $matchPosition = \intdiv($key, $slotCount);

            if ($matchPosition !== $lastPosition) {
                if (! $parsed) {
                    // Despite potentially being interested, nothing actually parsed text at the previous position, so add that character and continue onwards
                    $this->addPlainText((string) $cursor->getCurrentCharacter(), $block);
                    $cursor->advance();
                }

                $lastPosition    = $matchPosition;
                $currentPosition = $cursor->getPosition();
                // We've already gone past this point
                if ($currentPosition > $matchPosition) {
                    $parsed = true;
                    continue;
                }

                // We've skipped over some uninteresting text that should be added as a plain text node
                if ($currentPosition < $matchPosition) {
                    $cursor->advanceBy($matchPosition - $currentPosition);
                    $this->addPlainText($cursor->getPreviousText(), $block);
                }

                $parsed = false;
            } elseif ($parsed) {
                // A parser has successfully handled the text at this position; don't consider any others here
                continue;
            }

            // We're now at a potential start - see if this parser can handle it
            $parser = $this->parsers[$key % $slotCount >> 1][0];
            foreach (self::matchSets($matches) as $set) {
                if ($parsed = $parser->parse($inlineParserContext->withMatches($set))) {
                    break;
                }
            }
        }

        if (! $parsed) {
            $this->addPlainText((string) $cursor->getCurrentCharacter(), $block);
            $cursor->advance();
        }

        // Add any remaining text that wasn't parsed
        if (! $cursor->isAtEnd()) {
            $this->addPlainText($cursor->getRemainder(), $block);
        }

        // Process any delimiters that were found
        $delimiterStack = $inlineParserContext->getDelimiterStack();
        $delimiterStack->processDelimiters(null, $this->environment->getDelimiterProcessors());
        $delimiterStack->removeAll();

        // Combine adjacent text notes into one
        AdjacentTextMerger::mergeChildNodes($block);
    }

    /**
     * Use exact classes because subclasses can provide different match definitions.
     */
    private static function hasKnownTrigger(InlineParserInterface $parser): bool
    {
        // phpcs:ignore SlevomatCodingStandard.Classes.ModernClassNameReference.ClassNameReferencedViaFunctionCall -- PHP 7.4 compatibility.
        switch (\get_class($parser)) {
            case CoreInlineParser\AutolinkParser::class:
            case CoreInlineParser\BacktickParser::class:
            case CoreInlineParser\BangParser::class:
            case CoreInlineParser\CloseBracketParser::class:
            case CoreInlineParser\EntityParser::class:
            case CoreInlineParser\EscapableParser::class:
            case CoreInlineParser\HtmlInlineParser::class:
            case CoreInlineParser\OpenBracketParser::class:
            case EmailAutolinkParser::class:
            case UrlAutolinkParser::class:
            case TaskListItemMarkerParser::class:
            case NewlineParser::class:
                return true;
            case DelimiterParser::class:
                return $parser->hasOnlyStandardCharacters();
            default:
                return false;
        }
    }

    /**
     * @param string|non-empty-array<string>|list<non-empty-array<string>> $matches
     *
     * @return list<non-empty-array<string>>
     */
    private static function matchSets($matches): array
    {
        if (! \is_array($matches)) {
            return [[$matches]];
        }

        if (\is_array(\reset($matches))) {
            /** @var list<non-empty-array<string>> $sets */
            $sets = $matches;

            return $sets;
        }

        /** @var non-empty-array<string> $set */
        $set = $matches;

        return [$set];
    }

    private function addPlainText(string $text, AbstractBlock $container): void
    {
        $lastInline = $container->lastChild();
        if ($lastInline instanceof Text && ! $lastInline->data->has('delim')) {
            $lastInline->append($text);
        } else {
            $container->appendChild(new Text($text));
        }
    }

    /**
     * Given the current line, ask all the parsers which parts of the text they would be interested in parsing.
     *
     * The resulting array is ordered by character position, then by parser priority. Each key is
     * `(position * $slotCount) + (parser index * 2)`, plus one for a second match by the same parser at the
     * same position. Each value is what the parser's regex matched there: the matched text alone, or a
     * preg_match()-style array if there were sub-matches. For a regex using \K, which can match more than twice at
     * one position, the value is a list of such arrays.
     *
     * Lines can have a potential match at nearly every character, so this deliberately avoids
     * keeping any arrays per match.
     *
     * @param positive-int $slotCount
     *
     * @return array<int, string|non-empty-array<string>|list<non-empty-array<string>>>
     */
    private function matchParsers(string $contents, int $slotCount): array
    {
        $isMultibyte = ! \mb_check_encoding($contents, 'ASCII');

        // Marks the string as valid UTF-8, so that /u matches against it at an offset, such as those in
        // HtmlInlineParser and UrlAutolinkParser, don't re-validate the whole line on every call. Only needed
        // before PHP 8.3; newer versions don't repeat the validation here.
        \preg_match('//u', $contents);

        $ret            = [];
        $slot           = 0;
        $lastByteOffset = 0;
        $lastCharOffset = 0;
        $lastKey        = -1;

        // preg_replace_callback() hands over one match at a time, unlike preg_match_all() which would build several arrays per match
        $collect = static function (array $match) use (&$ret, &$slot, &$lastByteOffset, &$lastCharOffset, &$lastKey, $contents, $isMultibyte, $slotCount): string {
            // With PREG_OFFSET_CAPTURE, each element is the matched text and its byte offset
            /** @var array{string, int} $fullMatch */
            $fullMatch = $match[0];

            if ($isMultibyte) {
                // PREG_OFFSET_CAPTURE always returns the byte offset, not the char offset, which is annoying.
                // Matches arrive in ascending order, so convert incrementally from the previous position
                // instead of re-scanning from the start of the line for every match (which would be quadratic).
                $lastCharOffset += \mb_strlen(\substr($contents, $lastByteOffset, $fullMatch[1] - $lastByteOffset), 'UTF-8');
                $lastByteOffset  = $fullMatch[1];
                $offset          = $lastCharOffset;
            } else {
                $offset = $fullMatch[1];
            }

            $key = $offset * $slotCount + $slot;
            if ($key === $lastKey) {
                $key++;
            }

            $lastKey = $key;

            if (\count($match) === 1) {
                $ret[$key] = $fullMatch[0];

                return '';
            }

            // Remove the offsets, keeping only the matched text
            $matches = \array_column($match, 0);
            \assert($matches !== []);
            $ret[$key] = $matches;

            return '';
        };

        foreach ($this->parsers as $parserIndex => [, $regex, $isRegexMultibyte]) {
            $isUtf8Regex = $isMultibyte || $isRegexMultibyte;
            if ($isUtf8Regex) {
                $regex .= 'u';
            }

            // \K can report a match that starts before the previous one ended, which
            // preg_replace_callback() can't handle, so such regexes are collected the old way
            if (\strpos($regex, '\K') !== false) {
                foreach ($this->collectWithMatchAll($regex, $contents, $isMultibyte, $parserIndex * 2, $slotCount) as $key => $sets) {
                    $ret[$key] = $sets;
                }

                continue;
            }

            // preg_replace_callback() re-validates UTF-8 on every call, even for a line already known to be
            // valid, so skip it when the regex can't match at all
            if ($isUtf8Regex && \preg_match($regex, $contents) !== 1) {
                continue;
            }

            $slot           = $parserIndex * 2;
            $lastByteOffset = 0;
            $lastCharOffset = 0;
            $lastKey        = -1;

            if (\preg_replace_callback($regex, $collect, $contents, -1, $count, \PREG_OFFSET_CAPTURE) !== null) {
                continue;
            }

            // The regex failed part-way through the line, so ignore this parser entirely
            foreach ($ret as $key => $unused) {
                if ($key % $slotCount >> 1 === $parserIndex) {
                    unset($ret[$key]);
                }
            }
        }

        // Sort matches by position so we visit them in order
        \ksort($ret);

        return $ret;
    }

    /**
     * @param non-empty-string $regex
     * @param positive-int     $slotCount
     *
     * @return array<int, list<non-empty-array<string>>>
     */
    private function collectWithMatchAll(string $regex, string $contents, bool $isMultibyte, int $slot, int $slotCount): array
    {
        $found = [];
        if (! \preg_match_all($regex, $contents, $matches, \PREG_OFFSET_CAPTURE | \PREG_SET_ORDER)) {
            return $found;
        }

        $lastByteOffset = 0;
        $lastCharOffset = 0;
        foreach ($matches as $match) {
            if ($isMultibyte) {
                $lastCharOffset += \mb_strlen(\substr($contents, $lastByteOffset, $match[0][1] - $lastByteOffset), 'UTF-8');
                $lastByteOffset  = $match[0][1];
                $offset          = $lastCharOffset;
            } else {
                $offset = \intval($match[0][1]);
            }

            $matched = \array_column($match, 0);
            \assert($matched !== []);
            $found[$offset * $slotCount + $slot][] = $matched;
        }

        return $found;
    }
}
