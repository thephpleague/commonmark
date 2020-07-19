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

use League\CommonMark\Inline\Element as InlineElement;
use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Inline\Renderer\TextRenderer;
use League\CommonMark\Tests\Unit\FakeHtmlRenderer;
use PHPUnit\Framework\TestCase;

class TextRendererTest extends TestCase
{
    /**
     * @var TextRenderer
     */
    protected $renderer;

    protected function setUp(): void
    {
        $this->renderer = new TextRenderer();
    }

    public function testRender()
    {
        $inline = new Text('foo bar');
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertIsString($result);
        $this->assertStringContainsString('foo bar', $result);
    }

    public function testRenderWithInvalidType()
    {
        $this->expectException(\InvalidArgumentException::class);

        $inline = $this->getMockForAbstractClass(InlineElement\AbstractInline::class);
        $fakeRenderer = new FakeHtmlRenderer();

        $this->renderer->render($inline, $fakeRenderer);
    }
}
