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

namespace League\CommonMark\Tests\Unit\Event;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Event\DocumentPreParsedEvent;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Input\MarkdownInput;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Parser\MarkdownParser;
use PHPUnit\Framework\TestCase;

final class DocumentPreParsedEventTest extends TestCase
{
    public function testGetDocument(): void
    {
        $document = new Document();
        $markdown = new MarkdownInput('');

        $event = new DocumentPreParsedEvent($document, $markdown);

        $this->assertSame($document, $event->getDocument());
        $this->assertSame($markdown, $event->getMarkdown());
    }

    public function testEventDispatchedAtCorrectTime(): void
    {
        $wasCalled = false;

        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addEventListener(DocumentPreParsedEvent::class, static function (DocumentPreParsedEvent $event) use (&$wasCalled): void {
            $wasCalled = true;
        });

        $parser = new MarkdownParser($environment);
        $parser->parse('hello world');

        $this->assertTrue($wasCalled);
    }
}
