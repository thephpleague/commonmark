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

namespace League\CommonMark\Tests\Unit\Extension\CommonMark\Renderer\Block;

use League\CommonMark\Extension\CommonMark\Node\Block\ListBlock;
use League\CommonMark\Extension\CommonMark\Node\Block\ListData;
use League\CommonMark\Extension\CommonMark\Renderer\Block\ListBlockRenderer;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Tests\Unit\Renderer\FakeChildNodeRenderer;
use League\CommonMark\Util\HtmlElement;
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
        $fakeRenderer = new FakeChildNodeRenderer();
        $fakeRenderer->pretendChildrenExist();

        $result = $this->renderer->render($list, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('ol', $result->getTagName());
        $this->assertSame($expectedAttributeValue, $result->getAttribute('start'));
        $this->assertStringContainsString('::children::', $result->getContents(true));
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
        $fakeRenderer = new FakeChildNodeRenderer();
        $fakeRenderer->pretendChildrenExist();

        $result = $this->renderer->render($list, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('ul', $result->getTagName());
        $this->assertStringContainsString('::children::', $result->getContents(true));
        $this->assertEquals(['id' => 'foo'], $result->getAllAttributes());
    }

    public function testRenderWithInvalidType()
    {
        $this->expectException(\InvalidArgumentException::class);

        $inline = $this->getMockForAbstractClass(AbstractBlock::class);
        $fakeRenderer = new FakeChildNodeRenderer();

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
