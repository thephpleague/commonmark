<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\CommonMark\Tests\Unit\Extension\Footnote\Parser;

use League\CommonMark\Extension\Footnote\Node\Footnote;
use League\CommonMark\Extension\Footnote\Parser\FootnoteStartParser;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\MarkdownParserStateInterface;
use PHPUnit\Framework\TestCase;

final class FootnoteStartParserTest extends TestCase
{
    public function testDefinitionWithNonAsciiLabel(): void
    {
        $cursor = new Cursor('[^Über]: word');

        $start = (new FootnoteStartParser())->tryStart($cursor, $this->createStub(MarkdownParserStateInterface::class));

        $this->assertNotNull($start);
        $this->assertSame('word', $cursor->getRemainder());

        $block = [...$start->getBlockParsers()][0]->getBlock();
        $this->assertInstanceOf(Footnote::class, $block);
        $this->assertSame('Über', $block->getReference()->getLabel());
    }

    public function testDefinitionAfterNonAsciiCharacters(): void
    {
        $cursor = new Cursor('ÄÖÜ [^ref]: word');
        $cursor->advanceBy(3);

        $start = (new FootnoteStartParser())->tryStart($cursor, $this->createStub(MarkdownParserStateInterface::class));

        $this->assertNotNull($start);
        $this->assertSame('word', $cursor->getRemainder());

        $block = [...$start->getBlockParsers()][0]->getBlock();
        $this->assertInstanceOf(Footnote::class, $block);
        $this->assertSame('ref', $block->getReference()->getLabel());
    }
}
