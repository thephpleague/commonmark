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

namespace League\CommonMark\Tests\Unit\Node\Block;

use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Reference\ReferenceMap;
use League\CommonMark\Reference\ReferenceMapInterface;
use PHPUnit\Framework\TestCase;

final class DocumentTest extends TestCase
{
    public function testDefaultConstructorAndGetReferenceMap(): void
    {
        $document = new Document();

        $this->assertInstanceOf(ReferenceMap::class, $document->getReferenceMap());
    }

    public function testReferenceMapPassedIntoConstructor(): void
    {
        $map = $this->createMock(ReferenceMapInterface::class);

        $document = new Document($map);

        $this->assertSame($map, $document->getReferenceMap());
    }

    public function testCanContain(): void
    {
        $document = new Document();

        $block = $this->createMock(AbstractBlock::class);
        $this->assertTrue($document->canContain($block));
    }

    public function testIsCode(): void
    {
        $document = new Document();

        $this->assertFalse($document->isCode());
    }

    public function testMatchesNextLine(): void
    {
        $document = new Document();

        $cursor = $this->createMock(Cursor::class);
        $this->assertTrue($document->matchesNextLine($cursor));
    }
}
