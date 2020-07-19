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
use League\CommonMark\Inline\Element\Image;
use League\CommonMark\Inline\Renderer\ImageRenderer;
use League\CommonMark\Tests\Unit\FakeHtmlRenderer;
use League\CommonMark\Util\Configuration;
use PHPUnit\Framework\TestCase;

class ImageRendererTest extends TestCase
{
    /**
     * @var ImageRenderer
     */
    protected $renderer;

    protected function setUp(): void
    {
        $this->renderer = new ImageRenderer();
        $this->renderer->setConfiguration(new Configuration());
    }

    public function testRenderWithTitle()
    {
        $inline = new Image('http://example.com/foo.jpg', '::label::', '::title::');
        $inline->data['attributes'] = ['id' => '::id::', 'title' => '::title2::', 'label' => '::label2::', 'alt' => '::alt2::'];
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('img', $result->getTagName());
        $this->assertStringContainsString('http://example.com/foo.jpg', $result->getAttribute('src'));
        $this->assertStringContainsString('foo.jpg', $result->getAttribute('src'));
        $this->assertStringContainsString('::inlines::', $result->getAttribute('alt'));
        $this->assertStringContainsString('::title::', $result->getAttribute('title'));
        $this->assertStringContainsString('::id::', $result->getAttribute('id'));
    }

    public function testRenderWithoutTitle()
    {
        $inline = new Image('http://example.com/foo.jpg', '::label::');
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('img', $result->getTagName());
        $this->assertStringContainsString('http://example.com/foo.jpg', $result->getAttribute('src'));
        $this->assertStringContainsString('foo.jpg', $result->getAttribute('src'));
        $this->assertStringContainsString('::inlines::', $result->getAttribute('alt'));
        $this->assertNull($result->getAttribute('title'));
    }

    public function testRenderAllowUnsafeLink()
    {
        $this->renderer->setConfiguration(new Configuration([
            'allow_unsafe_links' => true,
        ]));

        $inline = new Image('javascript:void(0)');
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertStringContainsString('javascript:void(0)', $result->getAttribute('src'));
    }

    public function testRenderDisallowUnsafeLink()
    {
        $this->renderer->setConfiguration(new Configuration([
            'allow_unsafe_links' => false,
        ]));

        $inline = new Image('javascript:void(0)');
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('', $result->getAttribute('src'));
    }

    public function testRenderWithInvalidType()
    {
        $this->expectException(\InvalidArgumentException::class);

        $inline = $this->getMockForAbstractClass(InlineElement\AbstractInline::class);
        $fakeRenderer = new FakeHtmlRenderer();

        $this->renderer->render($inline, $fakeRenderer);
    }
}
