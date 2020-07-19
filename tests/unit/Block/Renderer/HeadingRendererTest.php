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
use League\CommonMark\Block\Element\Heading;
use League\CommonMark\Block\Renderer\HeadingRenderer;
use League\CommonMark\HtmlElement;
use League\CommonMark\Tests\Unit\FakeHtmlRenderer;
use PHPUnit\Framework\TestCase;

class HeadingRendererTest extends TestCase
{
    /**
     * @var HeadingRenderer
     */
    protected $renderer;

    protected function setUp(): void
    {
        $this->renderer = new HeadingRenderer();
    }

    /**
     * @param int    $level
     * @param string $expectedTag
     *
     * @dataProvider dataForTestRender
     */
    public function testRender($level, $expectedTag)
    {
        $block = new Heading($level, 'test');
        $block->data['attributes'] = ['id' => 'foo'];
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals($expectedTag, $result->getTagName());
        $this->assertStringContainsString('::inlines::', $result->getContents(true));
        $this->assertEquals(['id' => 'foo'], $result->getAllAttributes());
    }

    public function dataForTestRender()
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

    public function testRenderWithInvalidType()
    {
        $this->expectException(\InvalidArgumentException::class);

        $inline = $this->getMockForAbstractClass(BlockElement\AbstractBlock::class);
        $fakeRenderer = new FakeHtmlRenderer();

        $this->renderer->render($inline, $fakeRenderer);
    }
}
