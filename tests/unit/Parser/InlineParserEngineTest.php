<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Parser;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\CommonMark\Parser\Inline\CloseBracketParser;
use League\CommonMark\Extension\CommonMark\Parser\Inline\OpenBracketParser;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;
use League\CommonMark\Parser\InlineParserEngine;
use League\CommonMark\Reference\ReferenceMap;
use League\CommonMark\Tests\Unit\Parser\Inline\FakeInlineParser;
use PHPUnit\Framework\Attributes\RunInSeparateProcess as SeparateProcess;
use PHPUnit\Framework\TestCase;

final class InlineParserEngineTest extends TestCase
{
    public function testParseWithDefaultPriorityOrder(): void
    {
        $colorParser      = new FakeInlineParser(InlineParserMatch::string('brown'));
        $adjectiveParser  = new FakeInlineParser(InlineParserMatch::oneOf('quick', 'brown', 'lazy'));
        $fiveLetterParser = new FakeInlineParser(InlineParserMatch::regex('\b\w{5}\b'));

        $environment = new Environment();
        $environment->addInlineParser($colorParser);
        $environment->addInlineParser($adjectiveParser);
        $environment->addInlineParser($fiveLetterParser);

        $engine    = new InlineParserEngine($environment, new ReferenceMap());
        $paragraph = new Paragraph();
        $engine->parse('The quick brown fox jumps over the lazy dog', $paragraph);

        $this->assertSame(['brown'], $colorParser->getMatches());
        $this->assertSame(['quick', 'lazy'], $adjectiveParser->getMatches());
        $this->assertSame(['jumps'], $fiveLetterParser->getMatches());
    }

    public function testParseWithDifferentPriorityOrder(): void
    {
        $colorParser      = new FakeInlineParser(InlineParserMatch::string('brown'));
        $adjectiveParser  = new FakeInlineParser(InlineParserMatch::oneOf('quick', 'brown', 'lazy'));
        $fiveLetterParser = new FakeInlineParser(InlineParserMatch::regex('\b\w{5}\b'));

        $environment = new Environment();
        $environment->addInlineParser($colorParser, 100);
        $environment->addInlineParser($adjectiveParser, -100);
        $environment->addInlineParser($fiveLetterParser);

        $engine    = new InlineParserEngine($environment, new ReferenceMap());
        $paragraph = new Paragraph();
        $engine->parse('The quick brown fox jumps over the lazy dog', $paragraph);

        $this->assertSame(['brown'], $colorParser->getMatches());
        $this->assertSame(['lazy'], $adjectiveParser->getMatches());
        $this->assertSame(['quick', 'jumps'], $fiveLetterParser->getMatches());
    }

    public function testParseWithNoInlineParsers(): void
    {
        $environment = new Environment();
        $engine      = new InlineParserEngine($environment, new ReferenceMap());
        $paragraph   = new Paragraph();
        $engine->parse('The quick brown fox jumps over the lazy dog', $paragraph);

        $this->assertCount(1, $paragraph->children());
        $child = $paragraph->firstChild();
        $this->assertTrue($child instanceof Text);
        $this->assertSame('The quick brown fox jumps over the lazy dog', $child->getLiteral());
    }

    public function testPlainUtf8ParagraphAndGfmAutolinks(): void
    {
        $converter = new GithubFlavoredMarkdownConverter();

        $this->assertSame("<p>Обычный текст. 漢字</p>\n", (string) $converter->convert('Обычный текст. 漢字'));
        $this->assertSame(
            "<p>Почта <a href=\"mailto:a@example.com\">a@example.com</a></p>\n",
            (string) $converter->convert('Почта a@example.com')
        );
        $this->assertSame(
            "<p>Сайт <a href=\"http://www.example.com\">www.example.com</a></p>\n",
            (string) $converter->convert('Сайт www.example.com')
        );
        $this->assertSame(
            "<p>Сайт <a href=\"https://example.com\">https://example.com</a></p>\n",
            (string) $converter->convert('Сайт https://example.com')
        );
    }

    public function testParseWithMultipleMatchesAtMultibyteOffsets(): void
    {
        $parser = new FakeInlineParser(InlineParserMatch::oneOf('!', '?'));

        $environment = new Environment();
        $environment->addInlineParser($parser);

        $engine    = new InlineParserEngine($environment, new ReferenceMap());
        $paragraph = new Paragraph();
        $engine->parse('é!中?😀!', $paragraph);

        $this->assertSame(['!', '?', '!'], $parser->getMatches());
        $this->assertSame('é!中?😀!', $paragraph->firstChild()->getLiteral());
    }

    public function testRegexThatMatchesBeforeThePreviousMatchEnds(): void
    {
        // \K inside a lookbehind lets a match start before the previous one ended
        if (@\preg_match('/(?<=\Kx)y/', '') === false) {
            $this->markTestSkipped('This PCRE version does not allow \K in lookarounds');
        }

        [$calls, $text] = self::parseWithRecordingParser('(?<=\Kx)y|(?=x)|x', 'éxy');

        $this->assertSame([[1, ''], [1, 'x'], [1, 'xy']], $calls);
        $this->assertSame('éxy', $text);
    }

    public function testRegexThatMatchesTwiceAtOnePosition(): void
    {
        [$calls, $text] = self::parseWithRecordingParser('(?=x)|x', 'éx');

        $this->assertSame([[1, ''], [1, 'x']], $calls);
        $this->assertSame('éx', $text);
    }

    public function testRegexThatFailsPartWayThroughTheLineIsIgnored(): void
    {
        $limit = \ini_get('pcre.backtrack_limit');
        \ini_set('pcre.backtrack_limit', '100');

        try {
            // "a" matches before the regex hits the backtracking limit, but a regex that fails is ignored for the whole line
            [$calls, $text] = self::parseWithRecordingParser('a|(?:x+x+)+y', 'a ' . \str_repeat('x', 30));
        } finally {
            \ini_set('pcre.backtrack_limit', (string) $limit);
        }

        $this->assertSame([], $calls);
        $this->assertSame('a ' . \str_repeat('x', 30), $text);
    }

    /**
     * Parses a line with one inline parser that records each call and never parses anything
     *
     * @return array{list<array{int, string}>, string} the [position, full match] of each call, and the resulting text
     */
    private static function parseWithRecordingParser(string $regex, string $line): array
    {
        $parser = new class ($regex) implements InlineParserInterface {
            /** @var list<array{int, string}> */
            public array $calls = [];

            private string $regex;

            public function __construct(string $regex)
            {
                $this->regex = $regex;
            }

            public function getMatchDefinition(): InlineParserMatch
            {
                return InlineParserMatch::regex($this->regex);
            }

            public function parse(InlineParserContext $inlineContext): bool
            {
                $this->calls[] = [$inlineContext->getCursor()->getPosition(), $inlineContext->getFullMatch()];

                return false;
            }
        };

        $environment = new Environment();
        $environment->addInlineParser($parser);

        $paragraph = new Paragraph();
        (new InlineParserEngine($environment, new ReferenceMap()))->parse($line, $paragraph);

        $text = $paragraph->firstChild();
        \assert($text instanceof Text);

        return [$parser->calls, $text->getLiteral()];
    }

    /**
     * @see https://github.com/thephpleague/commonmark/issues/951
     *
     * @runInSeparateProcess to avoid polluting the global environment
     */
    #[SeparateProcess]
    public function testMultibyteDetectionRegressionFromIssue951(): void
    {
        \mb_internal_encoding('iso-8859-1'); // @phpstan-ignore-line

        $environment = new Environment();
        $environment->addInlineParser(new CloseBracketParser(), 30);
        $environment->addInlineParser(new OpenBracketParser(), 20);

        $engine    = new InlineParserEngine($environment, new ReferenceMap());
        $paragraph = new Paragraph();
        $engine->parse('AAA ÀÀ [label](https://url)', $paragraph);

        $this->assertCount(2, $paragraph->children());

        $text = $paragraph->firstChild();
        $this->assertTrue($text instanceof Text);
        $this->assertSame('AAA ÀÀ ', $text->getLiteral());

        $link = $paragraph->lastChild();
        $this->assertTrue($link instanceof Link);
        $this->assertSame('label', $link->firstChild()->getLiteral());
        $this->assertSame('https://url', $link->getUrl());
    }
}
