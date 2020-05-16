<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Extension\HeadingPermalink;

use League\CommonMark\Block\Element\Document;
use League\CommonMark\Block\Element\Heading;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Exception\InvalidOptionException;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalink;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkProcessor;
use League\CommonMark\Extension\HeadingPermalink\Slug\SlugGeneratorInterface;
use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Util\Configuration;
use PHPUnit\Framework\TestCase;

final class HeadingPermalinkProcessorTest extends TestCase
{
    public function testNoConstructorArgsUsesADefaultSlugGenerator()
    {
        $processor = new HeadingPermalinkProcessor();
        $processor->setConfiguration(new Configuration());

        $document = new Document();
        $document->appendChild($heading = new Heading(1, 'Test Heading'));
        $heading->appendChild(new Text('Test Heading'));

        $event = new DocumentParsedEvent($document);
        $processor($event);

        /** @var HeadingPermalink $headingLink */
        $headingLink = $document->firstChild()->firstChild();

        $this->assertSame('test-heading', $headingLink->getSlug());
    }

    public function testConstructorWithCustomSlugGenerator()
    {
        $processor = new HeadingPermalinkProcessor(new class() implements SlugGeneratorInterface {
            public function createSlug(string $input): string
            {
                return 'custom-slug';
            }
        });
        $processor->setConfiguration(new Configuration());

        $document = new Document();
        $document->appendChild($heading = new Heading(1, 'Test Heading'));
        $heading->appendChild(new Text('Test Heading'));

        $event = new DocumentParsedEvent($document);
        $processor($event);

        /** @var HeadingPermalink $headingLink */
        $headingLink = $document->firstChild()->firstChild();

        $this->assertSame('custom-slug', $headingLink->getSlug());
    }

    public function testCustomSlugGeneratorOptionOverridesConstructor()
    {
        $processor = new HeadingPermalinkProcessor(new class() implements SlugGeneratorInterface {
            public function createSlug(string $input): string
            {
                return 'slug-via-constructor';
            }
        });

        $overridingSlugGenerator = new class() implements SlugGeneratorInterface {
            public function createSlug(string $input): string
            {
                return 'slug-via-config';
            }
        };

        $processor->setConfiguration(new Configuration([
            'heading_permalink' => [
                'slug_generator' => $overridingSlugGenerator,
            ],
        ]));

        $document = new Document();
        $document->appendChild($heading = new Heading(1, 'Test Heading'));
        $heading->appendChild(new Text('Test Heading'));

        $event = new DocumentParsedEvent($document);
        $processor($event);

        /** @var HeadingPermalink $headingLink */
        $headingLink = $document->firstChild()->firstChild();

        $this->assertSame('slug-via-config', $headingLink->getSlug());
    }

    public function testInvalidSlugGeneratorOption()
    {
        $this->expectException(InvalidOptionException::class);

        $processor = new HeadingPermalinkProcessor();
        $processor->setConfiguration(new Configuration([
            'heading_permalink' => [
                'slug_generator' => function (string $text) { return md5($text); },
            ],
        ]));

        $processor(new DocumentParsedEvent(new Document()));
    }
}
