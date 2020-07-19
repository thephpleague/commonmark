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

namespace League\CommonMark\Tests\Unit\Inline\Renderer;

use League\CommonMark\HtmlElement;
use League\CommonMark\Inline\Element as InlineElement;
use League\CommonMark\Inline\Element\Strong;
use League\CommonMark\Inline\Renderer\StrongRenderer;
use League\CommonMark\Tests\Unit\FakeHtmlRenderer;
use PHPUnit\Framework\TestCase;

class StrongRendererTest extends TestCase
{
    /**
     * @var StrongRenderer
     */
    protected $renderer;

    protected function setUp(): void
    {
        $this->renderer = new StrongRenderer();
    }

    public function testRender()
    {
        $inline = new Strong();
        $inline->data['attributes'] = ['id' => 'foo'];
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('strong', $result->getTagName());
        $this->assertStringContainsString('::inlines::', $result->getContents(true));
        $this->assertEquals(['id' => 'foo'], $result->getAllAttributes());
    }

    public function testRenderWithInvalidType()
    {
        $this->expectException(\InvalidArgumentException::class);

        $inline = $this->getMockForAbstractClass(InlineElement\AbstractInline::class);
        $fakeRenderer = new FakeHtmlRenderer();

        $this->renderer->render($inline, $fakeRenderer);
    }
}
