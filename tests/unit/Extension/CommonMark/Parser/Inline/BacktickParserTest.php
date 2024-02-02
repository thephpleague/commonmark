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
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\InlineParserContext;
use League\CommonMark\Reference\ReferenceMapInterface;
use PHPUnit\Framework\TestCase;

final class BacktickParserTest extends TestCase
{
    /**
     * @dataProvider dataForTestParse
     */
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
}
