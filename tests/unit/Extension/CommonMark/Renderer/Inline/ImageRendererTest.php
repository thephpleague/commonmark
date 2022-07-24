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

namespace League\CommonMark\Tests\Unit\Extension\CommonMark\Renderer\Inline;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Exception\InvalidArgumentException;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Inline\HtmlInline;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Extension\CommonMark\Renderer\Inline\ImageRenderer;
use League\CommonMark\Node\Inline\AbstractInline;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Tests\Unit\Renderer\FakeChildNodeRenderer;
use League\CommonMark\Util\HtmlElement;
use League\Config\ConfigurationInterface;
use PHPUnit\Framework\TestCase;

final class ImageRendererTest extends TestCase
{
    private ImageRenderer $renderer;

    protected function setUp(): void
    {
        $this->renderer = new ImageRenderer();
        $this->renderer->setConfiguration($this->createConfiguration());
    }

    public function testRenderWithTitle(): void
    {
        $inline = new Image('http://example.com/foo.jpg', '::label::', '::title::');
        $inline->data->set('attributes', ['id' => '::id::', 'title' => '::title2::', 'label' => '::label2::', 'alt' => '::alt2::']);
        $fakeRenderer = new FakeChildNodeRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('img', $result->getTagName());
        $this->assertStringContainsString('http://example.com/foo.jpg', $result->getAttribute('src'));
        $this->assertStringContainsString('foo.jpg', $result->getAttribute('src'));
        $this->assertStringContainsString('::label::', $result->getAttribute('alt'));
        $this->assertStringContainsString('::title::', $result->getAttribute('title'));
        $this->assertStringContainsString('::id::', $result->getAttribute('id'));
    }

    public function testRenderWithoutTitle(): void
    {
        $inline       = new Image('http://example.com/foo.jpg', '::label::');
        $fakeRenderer = new FakeChildNodeRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('img', $result->getTagName());
        $this->assertStringContainsString('http://example.com/foo.jpg', $result->getAttribute('src'));
        $this->assertStringContainsString('foo.jpg', $result->getAttribute('src'));
        $this->assertStringContainsString('::label::', $result->getAttribute('alt'));
        $this->assertNull($result->getAttribute('title'));
    }

    public function testRenderWithInlineChildrenForAltText(): void
    {
        $inline = new Image('http://example.com/foo.jpg');
        $inline->appendChild(new Text('an "image" with '));
        $inline->appendChild(new HtmlInline('<escapable characters>'));
        $inline->appendChild(new Text(' in the & title'));

        $result = $this->renderer->render($inline, new FakeChildNodeRenderer());

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('img', $result->getTagName());
        $this->assertStringContainsString('http://example.com/foo.jpg', $result->getAttribute('src'));
        $this->assertStringContainsString('foo.jpg', $result->getAttribute('src'));
        $this->assertStringContainsString('an "image" with <escapable characters> in the & title', $result->getAttribute('alt'));
        $this->assertNull($result->getAttribute('title'));

        $this->assertSame('<img src="http://example.com/foo.jpg" alt="an &quot;image&quot; with &lt;escapable characters&gt; in the &amp; title" />', (string) $result);
    }

    public function testRenderAllowUnsafeLink(): void
    {
        $this->renderer->setConfiguration($this->createConfiguration([
            'allow_unsafe_links' => true,
        ]));

        $inline       = new Image('javascript:void(0)');
        $fakeRenderer = new FakeChildNodeRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertStringContainsString('javascript:void(0)', $result->getAttribute('src'));
    }

    public function testRenderDisallowUnsafeLink(): void
    {
        $this->renderer->setConfiguration($this->createConfiguration([
            'allow_unsafe_links' => false,
        ]));

        $inline       = new Image('javascript:void(0)');
        $fakeRenderer = new FakeChildNodeRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('', $result->getAttribute('src'));
    }

    public function testRenderWithInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $inline       = $this->getMockForAbstractClass(AbstractInline::class);
        $fakeRenderer = new FakeChildNodeRenderer();

        $this->renderer->render($inline, $fakeRenderer);
    }

    /**
     * @param array<string, mixed> $values
     */
    private function createConfiguration(array $values = []): ConfigurationInterface
    {
        $config = Environment::createDefaultConfiguration();
        (new CommonMarkCoreExtension())->configureSchema($config);

        $config->merge($values);

        return $config->reader();
    }
}
