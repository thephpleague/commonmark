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

namespace League\CommonMark\Tests\Unit\Extension\CommonMark\Renderer\Block;

use League\CommonMark\Exception\InvalidArgumentException;
use League\CommonMark\Extension\CommonMark\Node\Block\ListBlock;
use League\CommonMark\Extension\CommonMark\Node\Block\ListData;
use League\CommonMark\Extension\CommonMark\Renderer\Block\ListBlockRenderer;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Tests\Unit\Renderer\FakeChildNodeRenderer;
use League\CommonMark\Util\HtmlElement;
use PHPUnit\Framework\TestCase;

final class ListBlockRendererTest extends TestCase
{
    private ListBlockRenderer $renderer;

    protected function setUp(): void
    {
        $this->renderer = new ListBlockRenderer();
    }

    /**
     * @dataProvider dataForTestOrderedListStartingNumber
     *
     * @param mixed $expectedAttributeValue
     */
    public function testRenderOrderedList(?int $listStart = null, $expectedAttributeValue = null): void
    {
        $list         = $this->createOrderedListBlock($listStart);
        $fakeRenderer = new FakeChildNodeRenderer();
        $fakeRenderer->pretendChildrenExist();

        $result = $this->renderer->render($list, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('ol', $result->getTagName());
        $this->assertSame($expectedAttributeValue, $result->getAttribute('start'));
        $this->assertStringContainsString('::children::', $result->getContents(true));
        $this->assertEquals('foo', $result->getAttribute('id'));
    }

    /**
     * @return iterable<array<mixed>>
     */
    public static function dataForTestOrderedListStartingNumber(): iterable
    {
        return [
            [null, null],
            [0, '0'],
            [1, null],
            [2, '2'],
            [42, '42'],
        ];
    }

    public function testRenderUnorderedList(): void
    {
        $list         = $this->createUnorderedListBlock();
        $fakeRenderer = new FakeChildNodeRenderer();
        $fakeRenderer->pretendChildrenExist();

        $result = $this->renderer->render($list, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('ul', $result->getTagName());
        $this->assertStringContainsString('::children::', $result->getContents(true));
        $this->assertEquals(['id' => 'foo'], $result->getAllAttributes());
    }

    public function testRenderWithInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $inline       = $this->getMockForAbstractClass(AbstractBlock::class);
        $fakeRenderer = new FakeChildNodeRenderer();

        $this->renderer->render($inline, $fakeRenderer);
    }

    private function createOrderedListBlock(?int $start): ListBlock
    {
        $data        = new ListData();
        $data->type  = ListBlock::TYPE_ORDERED;
        $data->start = $start;

        $block = new ListBlock($data);
        $block->data->set('attributes/id', 'foo');

        return $block;
    }

    protected function createUnorderedListBlock(): ListBlock
    {
        $data       = new ListData();
        $data->type = ListBlock::TYPE_BULLET;

        $block = new ListBlock($data);
        $block->data->set('attributes/id', 'foo');

        return $block;
    }
}
