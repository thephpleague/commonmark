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
use League\CommonMark\Event\DocumentPreRenderEvent;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Renderer\HtmlRenderer;
use PHPUnit\Framework\TestCase;

final class DocumentPreRenderEventTest extends TestCase
{
    public function testGettersAndReplacers(): void
    {
        $document = new Document();

        $event = new DocumentPreRenderEvent($document, 'html');

        $this->assertSame($document, $event->getDocument());
        $this->assertSame('html', $event->getFormat());
    }

    public function testEventDispatchedAtCorrectTime(): void
    {
        $document = new Document();
        $document->appendChild($paragraph = new Paragraph());
        $paragraph->appendChild(new Text('Original text'));

        $wasCalled = false;

        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addEventListener(DocumentPreRenderEvent::class, static function (DocumentPreRenderEvent $event) use (&$wasCalled): void {
            $wasCalled = true;
            $event->getDocument()->firstChild()->replaceChildren([new Text('New text')]);
        });

        $renderer = new HtmlRenderer($environment);
        $result   = $renderer->renderDocument($document);

        $this->assertTrue($wasCalled);
        $this->assertStringContainsString('New text', (string) $result);
        $this->assertStringNotContainsString('Original text', (string) $result);
    }
}
