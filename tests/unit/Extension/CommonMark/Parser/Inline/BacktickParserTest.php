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

namespace League\CommonMark\Tests\Unit\Extension\CommonMark\Parser\Inline;

use League\CommonMark\Extension\CommonMark\Node\Inline\Code;
use League\CommonMark\Extension\CommonMark\Parser\Inline\BacktickParser;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Parser\InlineParserContext;
use League\CommonMark\Reference\ReferenceMapInterface;
use PHPUnit\Framework\TestCase;

class BacktickParserTest extends TestCase
{
    /**
     * @param $string
     * @param $expectedContents
     *
     * @dataProvider dataForTestParse
     */
    public function testParse($string, $expectedContents)
    {
        $paragraph = new Paragraph();
        $inlineContext = new InlineParserContext($string, $paragraph, $this->createMock(ReferenceMapInterface::class));

        // Move to just before the first backtick
        $firstBacktickPos = mb_strpos($string, '`', null, 'utf-8');
        $inlineContext->getCursor()->advanceBy($firstBacktickPos);

        $parser = new BacktickParser();
        $this->assertTrue($parser->parse($inlineContext));

        /** @var Code $codeBlock */
        $codeBlock = $paragraph->firstChild();
        $this->assertInstanceOf(Code::class, $codeBlock);

        $this->assertSame($expectedContents, $codeBlock->getLiteral());
    }

    /**
     * @return array
     */
    public function dataForTestParse()
    {
        return [
            ['This is `just` a test', 'just'],
            ['Из: твоя `feature` ветка', 'feature'],
            ['Из: твоя `тест` ветка', 'тест'],
        ];
    }
}
