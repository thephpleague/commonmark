<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Extension\SmartPunct;

use League\CommonMark\Extension\SmartPunct\Quote;
use League\CommonMark\Extension\SmartPunct\QuoteRenderer;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Tests\Unit\Renderer\FakeChildNodeRenderer;
use PHPUnit\Framework\TestCase;

/**
 * Tests the quote renderer
 */
final class QuoteRendererTest extends TestCase
{
    /** @var QuoteRenderer */
    private $renderer;

    /** @var ChildNodeRendererInterface */
    private $childRenderer;

    protected function setUp(): void
    {
        $this->renderer = new QuoteRenderer();
        $this->childRenderer = new FakeChildNodeRenderer();
    }

    public function testInvalidInlineType()
    {
        $this->expectException(\InvalidArgumentException::class);

        $inline = $this->createMock(Text::class);

        $this->renderer->render($inline, $this->childRenderer);
    }

    /**
     * @param string $character
     * @param string $expected
     *
     * @dataProvider dataForTestRender
     */
    public function testRender(string $character, string $expected)
    {
        $inline = new Quote($character);

        $this->assertEquals($expected, $this->renderer->render($inline, $this->childRenderer));
    }

    public function dataForTestRender()
    {
        // Single-quotes should render as an apostrophe
        yield ["'", '’'];

        // Double-quotes should render as an opening quote
        yield ['"', '“'];

        // Already-stylized quotes should be rendered as-is
        yield ['’', '’'];
        yield ['“', '“'];
    }
}
