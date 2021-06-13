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
use League\CommonMark\Event\DocumentRenderedEvent;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Output\RenderedContent;
use League\CommonMark\Output\RenderedContentInterface;
use League\CommonMark\Renderer\HtmlRenderer;
use PHPUnit\Framework\TestCase;

final class DocumentRenderedEventTest extends TestCase
{
    public function testGettersAndReplacers(): void
    {
        $output = $this->createMock(RenderedContentInterface::class);

        $event = new DocumentRenderedEvent($output);

        $this->assertSame($output, $event->getOutput());

        // Replace the output with something else - the getter should return something different now
        $event->replaceOutput($this->createMock(RenderedContentInterface::class));

        $this->assertNotSame($output, $event->getOutput());
    }

    public function testEventDispatchedAtCorrectTime(): void
    {
        $wasCalled = false;

        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addEventListener(DocumentRenderedEvent::class, static function (DocumentRenderedEvent $event) use (&$wasCalled): void {
            $wasCalled = true;
            $event->replaceOutput(new RenderedContent(new Document(), 'foo'));
        });

        $renderer = new HtmlRenderer($environment);
        $result   = $renderer->renderDocument(new Document());

        $this->assertTrue($wasCalled);
        $this->assertSame('foo', (string) $result);
    }
}
