<?php

/*
 * This file is part of the league/commonmark-ext-strikethrough package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com> and uAfrica.com (http://uafrica.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Ext\Strikethrough\Test;

use League\CommonMark\Block\Element\Paragraph;
use League\CommonMark\Ext\Strikethrough\Strikethrough;
use League\CommonMark\Ext\Strikethrough\StrikethroughParser;
use League\CommonMark\InlineParserContext;
use League\CommonMark\Reference\ReferenceMap;
use PHPUnit\Framework\TestCase;

class StrikethroughParserTest extends TestCase
{
    /**
     * @param $string
     * @param $expected
     *
     * @dataProvider dataForTestParse
     */
    public function testParse($string, $expected)
    {
        $nodeStub = $this->createMock(Paragraph::class);
        $nodeStub->expects($this->any())->method('getStringContent')->willReturn($string);
        $nodeStub
            ->expects($this->once())
            ->method('appendChild')
            ->with($this->callback(function (Strikethrough $s) use ($expected) {
                return $s instanceof Strikethrough && $expected === $s->getContent();
            }));
        $inline_context = new InlineParserContext($nodeStub, new ReferenceMap());

        // Move to just before the first tilde pair
        $first_tilde_pos = mb_strpos($string, '~~', null, 'utf-8');
        $inline_context->getCursor()->advanceBy($first_tilde_pos);

        $parser = new StrikethroughParser();
        $parser->parse($inline_context);
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
