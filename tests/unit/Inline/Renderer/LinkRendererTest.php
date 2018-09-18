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
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Inline\Renderer\LinkRenderer;
use League\CommonMark\Tests\Unit\FakeHtmlRenderer;
use League\CommonMark\Util\Configuration;
use PHPUnit\Framework\TestCase;

class LinkRendererTest extends TestCase
{
    /**
     * @var LinkRenderer
     */
    protected $renderer;

    protected function setUp()
    {
        $this->renderer = new LinkRenderer();
        $this->renderer->setConfiguration(new Configuration());
    }

    public function testRenderWithTitle()
    {
        $inline = new Link('http://example.com/foo.html', '::label::', '::title::');
        $inline->data['attributes'] = ['id' => '::id::', 'title' => '::title2::', 'href' => '::href2::'];
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('a', $result->getTagName());
        $this->assertContains('http://example.com/foo.html', $result->getAttribute('href'));
        $this->assertContains('::title::', $result->getAttribute('title'));
        $this->assertContains('::inlines::', $result->getContents(true));
        $this->assertContains('::id::', $result->getAttribute('id'));
    }

    public function testRenderWithoutTitle()
    {
        $inline = new Link('http://example.com/foo.html', '::label::');
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('a', $result->getTagName());
        $this->assertContains('http://example.com/foo.html', $result->getAttribute('href'));
        $this->assertNull($result->getAttribute('title'));
        $this->assertContains('::inlines::', $result->getContents(true));
    }

    public function testRenderAllowUnsafeLink()
    {
        $this->renderer->setConfiguration(new Configuration([
            'allow_unsafe_links' => true,
        ]));

        $inline = new Link('javascript:void(0)');
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertContains('javascript:void(0)', $result->getAttribute('href'));
    }

    public function testRenderDisallowUnsafeLink()
    {
        $this->renderer->setConfiguration(new Configuration([
            'allow_unsafe_links' => false,
        ]));

        $inline = new Link('javascript:void(0)');
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('', $result->getAttribute('href'));
    }

    public function testRenderUnsafeLinkWithSafeOption()
    {
        $this->renderer->setConfiguration(new Configuration([
            'safe' => true,
        ]));

        $inline = new Link('javascript:void(0)');
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('', $result->getAttribute('href'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRenderWithInvalidType()
    {
        $inline = $this->getMockForAbstractClass('League\CommonMark\Inline\Element\AbstractInline');
        $fakeRenderer = new FakeHtmlRenderer();

        $this->renderer->render($inline, $fakeRenderer);
    }

    public function testRenderWithExternalTarget()
    {
        $inline = new Link('http://example.com/foo.html', '::label::', '::title::');
        $inline->data['attributes'] = ['target' => '_blank'];
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('a', $result->getTagName());
        $this->assertContains('http://example.com/foo.html', $result->getAttribute('href'));
        $this->assertContains('noopener', $result->getAttribute('rel'));
        $this->assertContains('noreferrer', $result->getAttribute('rel'));
    }
}
