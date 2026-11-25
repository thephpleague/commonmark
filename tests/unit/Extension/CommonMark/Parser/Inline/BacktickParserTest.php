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

namespace League\CommonMark\Tests\Unit\Extension\CommonMark\Parser\Inline;

use League\CommonMark\Extension\CommonMark\Node\Inline\Code;
use League\CommonMark\Extension\CommonMark\Parser\Inline\BacktickParser;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\InlineParserContext;
use League\CommonMark\Reference\ReferenceMapInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class BacktickParserTest extends TestCase
{
    /**
     * @dataProvider dataForTestParse
     */
    #[DataProvider('dataForTestParse')]
    public function testParse(string $string, string $expectedContents): void
    {
        $paragraph     = new Paragraph();
        $cursor        = new Cursor($string);
        $inlineContext = new InlineParserContext($cursor, $paragraph, $this->createMock(ReferenceMapInterface::class));

        // Move to just before the first backtick
        $firstBacktickPos = \mb_strpos($string, '`', 0, 'utf-8');
        $cursor->advanceBy($firstBacktickPos);

        $parser = new BacktickParser();
        $this->assertTrue($parser->parse($inlineContext->withMatches([$cursor->getCurrentCharacter()])));

        $codeBlock = $paragraph->firstChild();
        \assert($codeBlock instanceof Code);
        $this->assertInstanceOf(Code::class, $codeBlock);

        $this->assertSame($expectedContents, $codeBlock->getLiteral());
    }

    /**
     * @return iterable<array<string>>
     */
    public static function dataForTestParse(): iterable
    {
        return [
            ['This is `just` a test', 'just'],
            ['Из: твоя `feature` ветка', 'feature'],
            ['Из: твоя `тест` ветка', 'тест'],
        ];
    }

    public function testParseOpenerPartwayThroughRun(): void
    {
        // As after an escape consumes the first backtick of "\``a`"
        [$parser, $cursor, $context, $paragraph] = $this->createParser('\\``a`');
        $cursor->advanceBy(2);

        $this->assertTrue($parser->parse($context->withMatches(['`'])));

        $code = $paragraph->firstChild();
        $this->assertInstanceOf(Code::class, $code);
        $this->assertSame('a', $code->getLiteral());
        $this->assertTrue($cursor->isAtEnd());
    }

    /**
     * @dataProvider dataForTestParseAfterFullScan
     */
    #[DataProvider('dataForTestParseAfterFullScan')]
    public function testParseAfterFullScan(string $string, ?string $expectedCode): void
    {
        [$parser, $cursor, $context, $paragraph] = $this->createParser($string);

        // The leading three-backtick run has no closer, so this scans to the end of the line
        $this->assertTrue($parser->parse($context->withMatches(['`'])));
        $this->assertSame(3, $cursor->getPosition());

        // Then open partway through the "``" run, after its escaped first backtick
        $cursor->advanceBy(\mb_strpos($string, '\\') + 2 - 3);
        $this->assertTrue($parser->parse($context->withMatches(['`'])));

        $last = $paragraph->lastChild();
        if ($expectedCode === null) {
            $this->assertInstanceOf(Text::class, $last);
            $this->assertSame('`', $last->getLiteral());
        } else {
            $this->assertInstanceOf(Code::class, $last);
            $this->assertSame($expectedCode, $last->getLiteral());
        }
    }

    /**
     * @return iterable<array{string, ?string}>
     */
    public static function dataForTestParseAfterFullScan(): iterable
    {
        yield 'one-backtick closer seen during the scan' => ['```x \\``y`', 'y'];
        yield 'no one-backtick run anywhere' => ['```x \\``y', null];
    }

    public function testParseWhenNotAtBacktick(): void
    {
        [$parser, $cursor, $context, $paragraph] = $this->createParser('a`b`');

        $this->assertFalse($parser->parse($context->withMatches(['`'])));
        $this->assertSame(0, $cursor->getPosition());
        $this->assertFalse($paragraph->hasChildren());
    }

    /**
     * @return array{BacktickParser, Cursor, InlineParserContext, Paragraph}
     */
    private function createParser(string $string): array
    {
        $paragraph = new Paragraph();
        $cursor    = new Cursor($string);
        $context   = new InlineParserContext($cursor, $paragraph, $this->createMock(ReferenceMapInterface::class));

        return [new BacktickParser(), $cursor, $context, $paragraph];
    }
}
