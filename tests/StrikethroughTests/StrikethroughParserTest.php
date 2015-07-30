<?php
namespace CommonMarkExt\Tests\Strikethrough;

use CommonMarkExt\Strikethrough\Strikethrough;
use CommonMarkExt\Strikethrough\StrikethroughParser;
use League\CommonMark\Cursor;
use League\CommonMark\InlineParserContext;

class StrikethroughParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $string
     * @param $expected
     *
     * @dataProvider dataForTestParse
     */
    public function testParse($string, $expected)
    {
        $cursor = new Cursor($string);
        // Move to just before the first tilde pair
        $first_tilde_pos = mb_strpos($string, '~~', null, 'utf-8');
        $cursor->advanceBy($first_tilde_pos);
        $inline_context = new InlineParserContext($cursor);
        $context_stub = $this->getMock('League\CommonMark\ContextInterface');
        $parser = new StrikethroughParser();
        $parser->parse($context_stub, $inline_context);
        $inlines = $inline_context->getInlines();
        $this->assertCount(1, $inlines);
        $this->assertTrue($inlines->first() instanceof Strikethrough);
        /** @var Strikethrough $text */
        $text = $inlines->first();
        $this->assertEquals($expected, $text->getContent());
    }

    /**
     * @return array
     */
    public function dataForTestParse()
    {
        return [
            ['This is just an ~~integration~~ unit test', 'integration'],
            ['Из: твоя ~~feature~~ ветка', 'feature'],
            ['Из: твоя ~~тест~~ ветка', 'тест'],
        ];
    }
}
