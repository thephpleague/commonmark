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

namespace League\CommonMark\Tests\Unit\Renderer\Block;

use League\CommonMark\Extension\CommonMark\Node\Block\ThematicBreak;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Renderer\Block\DocumentRenderer;
use League\CommonMark\Tests\Unit\Renderer\FakeChildNodeRenderer;
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
        $fakeRenderer = new FakeChildNodeRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertIsString($result);
        $this->assertSame('', $result);
    }

    public function testRenderDocument()
    {
        $block = new Document();
        $block->appendChild(new ThematicBreak());
        $fakeRenderer = new FakeChildNodeRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertIsString($result);
        $this->assertStringContainsString('::children::', $result);
    }

    public function testRenderWithInvalidType()
    {
        $this->expectException(\InvalidArgumentException::class);

        $inline = $this->getMockForAbstractClass(AbstractBlock::class);
        $fakeRenderer = new FakeChildNodeRenderer();

        $this->renderer->render($inline, $fakeRenderer);
    }
}
