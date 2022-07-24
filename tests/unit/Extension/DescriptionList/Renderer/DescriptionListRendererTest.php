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
use League\CommonMark\Extension\DescriptionList\Node\DescriptionList;
use League\CommonMark\Extension\DescriptionList\Node\DescriptionTerm;
use League\CommonMark\Extension\DescriptionList\Renderer\DescriptionListRenderer;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Tests\Unit\Renderer\FakeChildNodeRenderer;
use League\CommonMark\Util\HtmlElement;
use PHPUnit\Framework\TestCase;

final class DescriptionListRendererTest extends TestCase
{
    private DescriptionListRenderer $renderer;

    protected function setUp(): void
    {
        $this->renderer = new DescriptionListRenderer();
    }

    public function testRender(): void
    {
        $list = new DescriptionList();
        $list->appendChild(new DescriptionTerm());
        $list->appendChild(new Description());

        $result = $this->renderer->render($list, new FakeChildNodeRenderer());

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('dl', $result->getTagName());
        $this->assertStringContainsString('::children::', $result->getContents(true));
    }

    public function testRenderWithInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $block = $this->getMockForAbstractClass(AbstractBlock::class);

        $this->renderer->render($block, new FakeChildNodeRenderer());
    }
}
