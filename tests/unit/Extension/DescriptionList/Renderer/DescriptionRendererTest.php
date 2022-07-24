<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Extension\DescriptionList\Renderer;

use League\CommonMark\Exception\InvalidArgumentException;
use League\CommonMark\Extension\DescriptionList\Node\Description;
use League\CommonMark\Extension\DescriptionList\Renderer\DescriptionRenderer;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Tests\Unit\Renderer\FakeChildNodeRenderer;
use League\CommonMark\Util\HtmlElement;
use PHPUnit\Framework\TestCase;

final class DescriptionRendererTest extends TestCase
{
    private DescriptionRenderer $renderer;

    protected function setUp(): void
    {
        $this->renderer = new DescriptionRenderer();
    }

    public function testRender(): void
    {
        $description = new Description();
        $description->appendChild(new Paragraph());

        $result = $this->renderer->render($description, new FakeChildNodeRenderer());

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('dd', $result->getTagName());
        $this->assertStringContainsString('::children::', $result->getContents(true));
    }

    public function testRenderWithInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $block = $this->getMockForAbstractClass(AbstractBlock::class);

        $this->renderer->render($block, new FakeChildNodeRenderer());
    }
}
