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

namespace League\CommonMark\Tests\Unit\Renderer\Block;

use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\HtmlDecorator;
use League\CommonMark\Renderer\NodeRendererInterface;
use PHPUnit\Framework\TestCase;

final class HtmlDecoratorTest extends TestCase
{
    public function testRender(): void
    {
        $inner = $this->getMockForAbstractClass(NodeRendererInterface::class);
        $inner->method('render')->willReturn('INNER CONTENTS');

        $decorator = new HtmlDecorator($inner, 'div', ['class' => 'foo', 'id' => 'bar'], true);

        $this->assertSame('<div class="foo" id="bar">INNER CONTENTS</div>', (string) $decorator->render(
            $this->getMockForAbstractClass(Node::class),
            $this->getMockForAbstractClass(ChildNodeRendererInterface::class)
        ));
    }
}
