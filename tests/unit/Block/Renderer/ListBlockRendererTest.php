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

namespace League\CommonMark\Tests\Unit\Block\Renderer;

use League\CommonMark\Block\Element as BlockElement;
use League\CommonMark\Block\Element\ListBlock;
use League\CommonMark\Block\Element\ListData;
use League\CommonMark\Block\Renderer\ListBlockRenderer;
use League\CommonMark\HtmlElement;
use League\CommonMark\Tests\Unit\FakeHtmlRenderer;
use PHPUnit\Framework\TestCase;

class ListBlockRendererTest extends TestCase
{
    /**
     * @var ListBlockRenderer
     */
    protected $renderer;

    protected function setUp(): void
    {
        $this->renderer = new ListBlockRenderer();
    }

    /**
     * @param int|null $listStart
     * @param mixed    $expectedAttributeValue
     *
     * @dataProvider dataForTestOrderedListStartingNumber
     */
    public function testRenderOrderedList($listStart = null, $expectedAttributeValue = null)
    {
        $list = $this->createOrderedListBlock($listStart);
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($list, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('ol', $result->getTagName());
        $this->assertSame($expectedAttributeValue, $result->getAttribute('start'));
        $this->assertStringContainsString('::blocks::', $result->getContents(true));
        $this->assertEquals('foo', $result->getAttribute('id'));
    }

    public function dataForTestOrderedListStartingNumber()
    {
        return [
            [null, null],
            [0, '0'],
            [1, null],
            [2, '2'],
            [42, '42'],
        ];
    }

    public function testRenderUnorderedList()
    {
        $list = $this->createUnorderedListBlock();
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($list, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('ul', $result->getTagName());
        $this->assertStringContainsString('::blocks::', $result->getContents(true));
        $this->assertEquals(['id' => 'foo'], $result->getAllAttributes());
    }

    public function testRenderWithInvalidType()
    {
        $this->expectException(\InvalidArgumentException::class);

        $inline = $this->getMockForAbstractClass(BlockElement\AbstractBlock::class);
        $fakeRenderer = new FakeHtmlRenderer();

        $this->renderer->render($inline, $fakeRenderer);
    }

    /**
     * @param int $start
     *
     * @return ListBlock
     */
    private function createOrderedListBlock($start)
    {
        $data = new ListData();
        $data->type = ListBlock::TYPE_ORDERED;
        $data->start = $start;

        $block = new ListBlock($data);
        $block->data['attributes'] = ['id' => 'foo'];

        return $block;
    }

    /**
     * @return ListBlock
     */
    protected function createUnorderedListBlock()
    {
        $data = new ListData();
        $data->type = ListBlock::TYPE_BULLET;

        $block = new ListBlock($data);
        $block->data['attributes'] = ['id' => 'foo'];

        return $block;
    }
}
