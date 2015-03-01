<?php

namespace League\CommonMark\Tests\Inline\Parser;

use League\CommonMark\Cursor;
use League\CommonMark\Inline\Element\Code;
use League\CommonMark\InlineParserContext;
use League\CommonMark\Inline\Parser\BacktickParser;

class BacktickParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $string
     * @param $expectedContents
     *
     * @dataProvider dataForTestParse
     */
    public function testParse($string, $expectedContents)
    {
        $cursor = new Cursor($string);

        // Move to just before the first backtick
        $firstBacktickPos = mb_strpos($string, '`', null, 'utf-8');
        $cursor->advanceBy($firstBacktickPos);

        $inlineContext = new InlineParserContext($cursor);
        $contextStub = $this->getMock('League\CommonMark\ContextInterface');

        $parser = new BacktickParser();

        $parser->parse($contextStub, $inlineContext);

        $inlines = $inlineContext->getInlines();
        $this->assertCount(1, $inlines);
        $this->assertTrue($inlines->first() instanceof Code);
        /** @var Code $code */
        $code = $inlines->first();
        $this->assertEquals($expectedContents, $code->getContent());
    }

    /**
     * @return array
     */
    public function dataForTestParse()
    {
        return array(
            array('This is `just` a test', 'just'),
            array('Из: твоя `feature` ветка', 'feature'),
            array('Из: твоя `тест` ветка', 'тест'),
        );
    }
}
