<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com> and uAfrica.com (http://uafrica.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Extension\Strikethrough;

use League\CommonMark\Extension\Strikethrough\Strikethrough;
use League\CommonMark\Extension\Strikethrough\StrikethroughRenderer;
use League\CommonMark\HtmlElement;
use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Inline\Renderer\CodeRenderer;
use PHPUnit\Framework\TestCase;

class StrikethroughRendererTest extends TestCase
{
    /**
     * @var CodeRenderer
     */
    protected $renderer;

    protected function setUp(): void
    {
        $this->renderer = new StrikethroughRenderer();
    }

    public function testRender()
    {
        $inline = new Strikethrough();
        $inline->data['attributes'] = ['id' => 'some"&amp;id'];
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('del', $result->getTagName());
        $this->assertStringContainsString('::inlines::', $result->getContents(true));
        $this->assertEquals(['id' => 'some"&amp;id'], $result->getAllAttributes());
    }

    public function testRenderWithInvalidNodeType()
    {
        $this->expectException(\InvalidArgumentException::class);

        $inline = new Text('ruh roh');
        $fakeRenderer = new FakeHtmlRenderer();

        $this->renderer->render($inline, $fakeRenderer);
    }
}
