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
use League\CommonMark\Block\Element\Document;
use League\CommonMark\Block\Renderer\DocumentRenderer;
use League\CommonMark\Tests\Unit\FakeEmptyHtmlRenderer;
use League\CommonMark\Tests\Unit\FakeHtmlRenderer;
use PHPUnit\Framework\TestCase;

class DocumentRendererTest extends TestCase
{
    /**
     * @var DocumentRenderer
     */
    protected $renderer;

    protected function setUp(): void
    {
        $this->renderer = new DocumentRenderer();
    }

    public function testRenderEmptyDocument()
    {
        $block = new Document();
        $fakeRenderer = new FakeEmptyHtmlRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertIsString($result);
        $this->assertEmpty($result);
    }

    public function testRenderDocument()
    {
        $block = new Document();
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertIsString($result);
        $this->assertStringContainsString('::blocks::', $result);
    }

    public function testRenderWithInvalidType()
    {
        $this->expectException(\InvalidArgumentException::class);

        $inline = $this->getMockForAbstractClass(BlockElement\AbstractBlock::class);
        $fakeRenderer = new FakeHtmlRenderer();

        $this->renderer->render($inline, $fakeRenderer);
    }
}
