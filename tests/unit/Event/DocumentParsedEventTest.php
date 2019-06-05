<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Event;

use League\CommonMark\Block\Element\Document;
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\Event\DocumentParsedEvent;
use PHPUnit\Framework\TestCase;

final class DocumentParsedEventTest extends TestCase
{
    public function testGetDocument()
    {
        $document = new Document();

        $event = new DocumentParsedEvent($document);

        $this->assertSame($document, $event->getDocument());
    }

    public function testEventDispatchedAtCorrectTime()
    {
        $wasCalled = false;

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addEventListener(DocumentParsedEvent::class, function (DocumentParsedEvent $event) use (&$wasCalled) {
            $wasCalled = true;
        });

        $parser = new DocParser($environment);
        $parser->parse('hello world');

        $this->assertTrue($wasCalled);
    }
}
