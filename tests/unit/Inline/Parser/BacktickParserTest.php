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

namespace League\CommonMark\Tests\Unit\Inline\Parser;

use League\CommonMark\Block\Element as BlockElement;
use League\CommonMark\Inline\Element\Code;
use League\CommonMark\Inline\Parser\BacktickParser;
use League\CommonMark\InlineParserContext;
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
        $nodeStub = $this->createMock(BlockElement\AbstractStringContainerBlock::class);
        $nodeStub->expects($this->any())->method('getStringContent')->willReturn($string);
        $nodeStub
            ->expects($this->once())
            ->method('appendChild')
            ->with($this->callback(function (Code $code) use ($expectedContents) {
                return $code instanceof Code && $expectedContents === $code->getContent();
            }));
        $inlineContext = new InlineParserContext($nodeStub, $this->createMock(ReferenceMapInterface::class));

        // Move to just before the first backtick
        $firstBacktickPos = mb_strpos($string, '`', null, 'utf-8');
        $inlineContext->getCursor()->advanceBy($firstBacktickPos);

        $parser = new BacktickParser();
        $parser->parse($inlineContext);
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
