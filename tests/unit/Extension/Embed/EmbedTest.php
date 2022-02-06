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
use PHPUnit\Framework\TestCase;

final class EmbedTest extends TestCase
{
    public function testConstructorGettersAndSetters(): void
    {
        $embed = new Embed('https://www.youtube.com/watch?v=dQw4w9WgXcQ');

        $this->assertSame('https://www.youtube.com/watch?v=dQw4w9WgXcQ', $embed->getUrl());
        $this->assertNull($embed->getEmbedCode());

        $embed->setEmbedCode('<iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>');
        $this->assertSame('<iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>', $embed->getEmbedCode());
    }
}
