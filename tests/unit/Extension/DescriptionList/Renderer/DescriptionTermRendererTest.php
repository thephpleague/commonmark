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
use League\CommonMark\Extension\DescriptionList\Node\DescriptionTerm;
use League\CommonMark\Extension\DescriptionList\Renderer\DescriptionTermRenderer;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Tests\Unit\Renderer\FakeChildNodeRenderer;
use League\CommonMark\Util\HtmlElement;
use PHPUnit\Framework\TestCase;

final class DescriptionTermRendererTest extends TestCase
{
    private DescriptionTermRenderer $renderer;

    protected function setUp(): void
    {
        $this->renderer = new DescriptionTermRenderer();
    }

    public function testRender(): void
    {
        $term = new DescriptionTerm();
        $term->appendChild(new Text('whatever'));

        $result = $this->renderer->render($term, new FakeChildNodeRenderer());

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('dt', $result->getTagName());
        $this->assertStringContainsString('::children::', $result->getContents(true));
    }

    public function testRenderWithInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $block = $this->getMockForAbstractClass(AbstractBlock::class);

        $this->renderer->render($block, new FakeChildNodeRenderer());
    }
}
