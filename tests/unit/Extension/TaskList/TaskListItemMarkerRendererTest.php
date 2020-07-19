<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Extension\TaskList;

use League\CommonMark\ElementRendererInterface;
use League\CommonMark\Extension\TaskList\TaskListItemMarker;
use League\CommonMark\Extension\TaskList\TaskListItemMarkerRenderer;
use League\CommonMark\HtmlElement;
use League\CommonMark\Inline\Element\AbstractInline;
use PHPUnit\Framework\TestCase;

class TaskListItemMarkerRendererTest extends TestCase
{
    public function testWithCheckedItem()
    {
        $renderer = new TaskListItemMarkerRenderer();
        $htmlRenderer = $this->getMockForAbstractClass(ElementRendererInterface::class);

        $item = new TaskListItemMarker(true);

        $result = $renderer->render($item, $htmlRenderer);

        $this->assertInstanceOf(HtmlElement::class, $result);
        $this->assertSame('input', $result->getTagName());
        $this->assertSame('checkbox', $result->getAttribute('type'));
        $this->assertNotNull($result->getAttribute('checked'));
    }

    public function testWithUncheckedItem()
    {
        $renderer = new TaskListItemMarkerRenderer();
        $htmlRenderer = $this->getMockForAbstractClass(ElementRendererInterface::class);

        $item = new TaskListItemMarker(false);

        $result = $renderer->render($item, $htmlRenderer);

        $this->assertInstanceOf(HtmlElement::class, $result);
        $this->assertSame('input', $result->getTagName());
        $this->assertSame('checkbox', $result->getAttribute('type'));
        $this->assertNull($result->getAttribute('checked'));
    }

    public function testWithInvalidInlineElement()
    {
        $this->expectException(\InvalidArgumentException::class);

        $renderer = new TaskListItemMarkerRenderer();
        $htmlRenderer = $this->getMockForAbstractClass(ElementRendererInterface::class);

        $item = $this->getMockForAbstractClass(AbstractInline::class);

        $renderer->render($item, $htmlRenderer);
    }
}
