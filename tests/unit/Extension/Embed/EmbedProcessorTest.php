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

use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\Embed\Embed;
use League\CommonMark\Extension\Embed\EmbedAdapterInterface;
use League\CommonMark\Extension\Embed\EmbedProcessor;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Block\Paragraph;
use PHPUnit\Framework\TestCase;

final class EmbedProcessorTest extends TestCase
{
    public function testUpdatesEmbeds(): void
    {
        $adapter   = new FakeAdapter();
        $processor = new EmbedProcessor($adapter);

        $document = new Document();
        $document->appendChild($embed1 = new Embed('https://www.youtube.com/watch?v=dQw4w9WgXcQ'));
        $document->appendChild($embed2 = new Embed('https://www.youtube.com/watch?v=jsYAYXeVtoM'));

        $processor(new DocumentParsedEvent($document));

        $this->assertSame([$embed1, $embed2], $adapter->getUpdatedEmbeds());
        $this->assertNotNull($embed1->getEmbedCode());
        $this->assertNotNull($embed2->getEmbedCode());
    }

    public function testNoUpdatesEmbedsWithoutEmbeds(): void
    {
        $adapter = $this->getMockBuilder(EmbedAdapterInterface::class)->getMock();
        $adapter->expects($this->never())
            ->method('updateEmbeds');
        $processor = new EmbedProcessor($adapter);

        $document = new Document();

        $processor(new DocumentParsedEvent($document));
    }

    public function testWithFallbackRemove(): void
    {
        // A fake adapter that doesn't do anything (like updating the embed codes)
        $adapter = $this->getMockForAbstractClass(EmbedAdapterInterface::class);

        $processor = new EmbedProcessor($adapter, EmbedProcessor::FALLBACK_REMOVE);

        $document = new Document();
        $document->appendChild(new Embed('https://www.youtube.com/watch?v=dQw4w9WgXcQ'));
        $document->appendChild(new Embed('https://www.youtube.com/watch?v=jsYAYXeVtoM'));

        $processor(new DocumentParsedEvent($document));

        $this->assertFalse($document->hasChildren());
    }

    public function testWithFallbackLink(): void
    {
        // A fake adapter that doesn't do anything (like updating the embed codes)
        $adapter = $this->getMockForAbstractClass(EmbedAdapterInterface::class);

        $processor = new EmbedProcessor($adapter, EmbedProcessor::FALLBACK_LINK);

        $document = new Document();
        $document->appendChild(new Embed('https://www.youtube.com/watch?v=dQw4w9WgXcQ'));
        $document->appendChild(new Embed('https://www.youtube.com/watch?v=jsYAYXeVtoM'));

        $processor(new DocumentParsedEvent($document));

        $this->assertInstanceOf(Paragraph::class, $document->firstChild());
        $this->assertInstanceOf(Link::class, $document->firstChild()->firstChild());
        $this->assertSame('https://www.youtube.com/watch?v=dQw4w9WgXcQ', $document->firstChild()->firstChild()->getUrl());

        $this->assertInstanceOf(Paragraph::class, $document->lastChild());
        $this->assertInstanceOf(Link::class, $document->lastChild()->firstChild());
        $this->assertSame('https://www.youtube.com/watch?v=jsYAYXeVtoM', $document->lastChild()->firstChild()->getUrl());
    }
}
