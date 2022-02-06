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
use League\CommonMark\Extension\Embed\EmbedParser;
use League\CommonMark\Node\Block\AbstractBlock;
use PHPUnit\Framework\TestCase;

final class EmbedParserTest extends TestCase
{
    public function testEverything(): void
    {
        $url    = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
        $parser = new EmbedParser($url);

        $block = $parser->getBlock();
        self::assertInstanceOf(Embed::class, $block);
        \assert($block instanceof Embed);
        self::assertSame($url, $block->getUrl());

        self::assertFalse($parser->isContainer());
        self::assertFalse($parser->canHaveLazyContinuationLines());
        self::assertFalse($parser->canContain($this->getMockForAbstractClass(AbstractBlock::class)));
    }
}
