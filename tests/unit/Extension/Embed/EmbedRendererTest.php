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

namespace League\CommonMark\Tests\Unit\Extension\Embed;

use League\CommonMark\Extension\Embed\Embed;
use League\CommonMark\Extension\Embed\EmbedRenderer;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use PHPUnit\Framework\TestCase;

final class EmbedRendererTest extends TestCase
{
    public function testRender(): void
    {
        $renderer = new EmbedRenderer();

        $embed = new Embed('https://www.youtube.com/watch?v=dQw4w9WgXcQ');
        $embed->setEmbedCode('<iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>');

        self::assertSame('<iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>', $renderer->render($embed, $this->getMockForAbstractClass(ChildNodeRendererInterface::class)));
    }
}
