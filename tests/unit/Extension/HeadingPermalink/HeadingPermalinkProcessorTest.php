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

namespace League\CommonMark\Tests\Unit\Extension\HeadingPermalink;

use League\CommonMark\Configuration\Configuration;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Exception\InvalidOptionException;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalink;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkProcessor;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Normalizer\TextNormalizerInterface;
use PHPUnit\Framework\TestCase;

final class HeadingPermalinkProcessorTest extends TestCase
{
    public function testNoConstructorArgsUsesADefaultSlugNormalizer(): void
    {
        $processor = new HeadingPermalinkProcessor();
        $processor->setConfiguration(new Configuration());

        $document = new Document();
        $document->appendChild($heading = new Heading(1));
        $heading->appendChild(new Text('Test Heading'));

        $event = new DocumentParsedEvent($document);
        $processor($event);

        $headingLink = $document->firstChild()->firstChild();
        \assert($headingLink instanceof HeadingPermalink);

        $this->assertSame('test-heading', $headingLink->getSlug());
    }

    public function testConstructorWithCustomSlugNormalizer(): void
    {
        $processor = new HeadingPermalinkProcessor(new class () implements TextNormalizerInterface {
            /**
             * {@inheritDoc}
             */
            public function normalize(string $text, $context = null): string
            {
                return 'custom-slug';
            }
        });
        $processor->setConfiguration(new Configuration());

        $document = new Document();
        $document->appendChild($heading = new Heading(1));
        $heading->appendChild(new Text('Test Heading'));

        $event = new DocumentParsedEvent($document);
        $processor($event);

        $headingLink = $document->firstChild()->firstChild();
        \assert($headingLink instanceof HeadingPermalink);

        $this->assertSame('custom-slug', $headingLink->getSlug());
    }

    public function testCustomSlugNormalizerOptionOverridesConstructor(): void
    {
        $processor = new HeadingPermalinkProcessor(new class () implements TextNormalizerInterface {
            /**
             * {@inheritDoc}
             */
            public function normalize(string $text, $context = null): string
            {
                return 'slug-via-constructor';
            }
        });

        $overridingSlugNormalizer = new class () implements TextNormalizerInterface {
            /**
             * {@inheritDoc}
             */
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
        $document->appendChild($heading = new Heading(1));
        $heading->appendChild(new Text('Test Heading'));

        $event = new DocumentParsedEvent($document);
        $processor($event);

        $headingLink = $document->firstChild()->firstChild();
        \assert($headingLink instanceof HeadingPermalink);

        $this->assertSame('slug-via-config', $headingLink->getSlug());
    }

    public function testInvalidSlugNormalizerOption(): void
    {
        $this->expectException(InvalidOptionException::class);

        $processor = new HeadingPermalinkProcessor();
        $processor->setConfiguration(new Configuration([
            'heading_permalink' => [
                'slug_normalizer' => static function (string $text): string {
                    return \md5($text);
                },
            ],
        ]));

        $processor(new DocumentParsedEvent(new Document()));
    }
}
