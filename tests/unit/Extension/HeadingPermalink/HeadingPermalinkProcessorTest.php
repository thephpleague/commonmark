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
use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Normalizer\TextNormalizerInterface;
use League\CommonMark\Util\Configuration;
use PHPUnit\Framework\TestCase;

final class HeadingPermalinkProcessorTest extends TestCase
{
    public function testNoConstructorArgsUsesADefaultSlugNormalizer()
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

    public function testConstructorWithCustomSlugNormalizer()
    {
        $processor = new HeadingPermalinkProcessor(new class() implements TextNormalizerInterface {
            public function normalize(string $text, $context = null): string
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

    public function testCustomSlugNormalizerOptionOverridesConstructor()
    {
        $processor = new HeadingPermalinkProcessor(new class() implements TextNormalizerInterface {
            public function normalize(string $text, $context = null): string
            {
                return 'slug-via-constructor';
            }
        });

        $overridingSlugNormalizer = new class() implements TextNormalizerInterface {
            public function normalize(string $text, $context = null): string
            {
                return 'slug-via-config';
            }
        };

        $processor->setConfiguration(new Configuration([
            'heading_permalink' => [
                'slug_normalizer' => $overridingSlugNormalizer,
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

    public function testInvalidSlugNormalizerOption()
    {
        $this->expectException(InvalidOptionException::class);

        $processor = new HeadingPermalinkProcessor();
        $processor->setConfiguration(new Configuration([
            'heading_permalink' => [
                'slug_normalizer' => function (string $text) {
                    return md5($text);
                },
            ],
        ]));

        $processor(new DocumentParsedEvent(new Document()));
    }

    public function testDuplicateSlugsAreMadeUnique(): void
    {
        $processor = new HeadingPermalinkProcessor();
        $processor->setConfiguration(new Configuration());

        $document = new Document();
        $document->appendChild($heading1 = new Heading(1, 'Test Heading'));
        $heading1->appendChild(new Text('Test Heading'));
        $document->appendChild($heading2 = new Heading(1, 'Test Heading'));
        $heading2->appendChild(new Text('Test Heading'));
        $document->appendChild($heading3 = new Heading(1, 'Test Heading 1'));
        $heading3->appendChild(new Text('Test Heading 1'));
        $document->appendChild($heading4 = new Heading(1, 'Test Heading'));
        $heading4->appendChild(new Text('Test Heading'));

        $event = new DocumentParsedEvent($document);
        $processor($event);

        /** @var HeadingPermalink $headingLink1 */
        $headingLink1 = $heading1->firstChild();
        $this->assertSame('test-heading', $headingLink1->getSlug());

        /** @var HeadingPermalink $headingLink2 */
        $headingLink2 = $heading2->firstChild();
        $this->assertSame('test-heading-1', $headingLink2->getSlug());

        /** @var HeadingPermalink $headingLink3 */
        $headingLink3 = $heading3->firstChild();
        $this->assertSame('test-heading-1-1', $headingLink3->getSlug());

        /** @var HeadingPermalink $headingLink4 */
        $headingLink4 = $heading4->firstChild();
        $this->assertSame('test-heading-2', $headingLink4->getSlug());

        // Test with a different document
        $document2 = new Document();
        $document2->appendChild($heading5 = new Heading(1, 'Test Heading'));
        $heading5->appendChild(new Text('Test Heading'));
        $document2->appendChild($heading6 = new Heading(1, 'Test Heading'));
        $heading6->appendChild(new Text('Test Heading'));

        $event = new DocumentParsedEvent($document2);
        $processor($event);

        /** @var HeadingPermalink $headingLink5 */
        $headingLink5 = $heading5->firstChild();
        $this->assertSame('test-heading', $headingLink5->getSlug());

        /** @var HeadingPermalink $headingLink6 */
        $headingLink6 = $heading6->firstChild();
        $this->assertSame('test-heading-1', $headingLink6->getSlug());
    }
}
