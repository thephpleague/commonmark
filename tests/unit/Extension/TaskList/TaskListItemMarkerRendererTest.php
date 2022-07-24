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

namespace League\CommonMark\Tests\Unit\Extension\TaskList;

use League\CommonMark\Exception\InvalidArgumentException;
use League\CommonMark\Extension\TaskList\TaskListItemMarker;
use League\CommonMark\Extension\TaskList\TaskListItemMarkerRenderer;
use League\CommonMark\Node\Inline\AbstractInline;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use PHPUnit\Framework\TestCase;

final class TaskListItemMarkerRendererTest extends TestCase
{
    public function testWithCheckedItem(): void
    {
        $renderer     = new TaskListItemMarkerRenderer();
        $htmlRenderer = $this->getMockForAbstractClass(ChildNodeRendererInterface::class);

        $item = new TaskListItemMarker(true);

        $result = $renderer->render($item, $htmlRenderer);

        $this->assertInstanceOf(HtmlElement::class, $result);
        $this->assertSame('input', $result->getTagName());
        $this->assertSame('checkbox', $result->getAttribute('type'));
        $this->assertNotNull($result->getAttribute('checked'));
    }

    public function testWithUncheckedItem(): void
    {
        $renderer     = new TaskListItemMarkerRenderer();
        $htmlRenderer = $this->getMockForAbstractClass(ChildNodeRendererInterface::class);

        $item = new TaskListItemMarker(false);

        $result = $renderer->render($item, $htmlRenderer);

        $this->assertInstanceOf(HtmlElement::class, $result);
        $this->assertSame('input', $result->getTagName());
        $this->assertSame('checkbox', $result->getAttribute('type'));
        $this->assertNull($result->getAttribute('checked'));
    }

    public function testWithInvalidInlineElement(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $renderer     = new TaskListItemMarkerRenderer();
        $htmlRenderer = $this->getMockForAbstractClass(ChildNodeRendererInterface::class);

        $item = $this->getMockForAbstractClass(AbstractInline::class);

        $renderer->render($item, $htmlRenderer);
    }
}
