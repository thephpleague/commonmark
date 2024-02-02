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
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Renderer\Block\HeadingRenderer;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Tests\Unit\Renderer\FakeChildNodeRenderer;
use League\CommonMark\Util\HtmlElement;
use PHPUnit\Framework\TestCase;

final class HeadingRendererTest extends TestCase
{
    private HeadingRenderer $renderer;

    protected function setUp(): void
    {
        $this->renderer = new HeadingRenderer();
    }

    /**
     * @dataProvider dataForTestRender
     */
    public function testRender(int $level, string $expectedTag): void
    {
        $block = new Heading($level);
        $block->data->set('attributes/id', 'foo');
        $fakeRenderer = new FakeChildNodeRenderer();
        $fakeRenderer->pretendChildrenExist();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals($expectedTag, $result->getTagName());
        $this->assertStringContainsString('::children::', $result->getContents(true));
        $this->assertEquals(['id' => 'foo'], $result->getAllAttributes());
    }

    /**
     * @return iterable<array<mixed>>
     */
    public static function dataForTestRender(): iterable
    {
        return [
            [1, 'h1'],
            [2, 'h2'],
            [3, 'h3'],
            [4, 'h4'],
            [5, 'h5'],
            [6, 'h6'],
        ];
    }

    public function testRenderWithInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $inline       = $this->getMockForAbstractClass(AbstractBlock::class);
        $fakeRenderer = new FakeChildNodeRenderer();

        $this->renderer->render($inline, $fakeRenderer);
    }
}
