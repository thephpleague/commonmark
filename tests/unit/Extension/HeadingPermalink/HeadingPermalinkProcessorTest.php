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

use League\CommonMark\Environment\Environment;
use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalink;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkProcessor;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Normalizer\TextNormalizerInterface;
use League\Config\Exception\InvalidConfigurationException;
use PHPUnit\Framework\TestCase;

final class HeadingPermalinkProcessorTest extends TestCase
{
    public function testUsesDefaultSlugNormalizer(): void
    {
        $processor = new HeadingPermalinkProcessor();
        $processor->setEnvironment($this->createEnvironment());

        $document = new Document();
        $document->appendChild($heading = new Heading(1));
        $heading->appendChild(new Text('Test Heading'));

        $event = new DocumentParsedEvent($document);
        $processor($event);

        $headingLink = $document->firstChild()->firstChild();
        \assert($headingLink instanceof HeadingPermalink);

        $this->assertSame('test-heading', $headingLink->getSlug());
    }

    public function testCustomSlugNormalizerOption(): void
    {
        $processor = new HeadingPermalinkProcessor();

        $overridingSlugNormalizer = new class () implements TextNormalizerInterface {
            /**
             * {@inheritDoc}
             */
            public function normalize(string $text, $context = null): string
            {
                return 'slug-via-config';
            }
        };

        $processor->setEnvironment($this->createEnvironment([
            'slug_normalizer' => [
                'instance' => $overridingSlugNormalizer,
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
        $this->expectException(InvalidConfigurationException::class);

        $processor = new HeadingPermalinkProcessor();
        $processor->setEnvironment($this->createEnvironment([
            'slug_normalizer' => [
                'instance' => static function (string $text): string {
                    return \md5($text);
                },
            ],
        ]));
    }

    public function testDuplicateSlugsAreMadeUnique(): void
    {
        $processor = new HeadingPermalinkProcessor();
        $processor->setEnvironment($this->createEnvironment());

        $document = new Document();
        $document->appendChild($heading1 = new Heading(1));
        $heading1->appendChild(new Text('Test Heading'));
        $document->appendChild($heading2 = new Heading(1));
        $heading2->appendChild(new Text('Test Heading'));
        $document->appendChild($heading3 = new Heading(1));
        $heading3->appendChild(new Text('Test Heading 1'));
        $document->appendChild($heading4 = new Heading(1));
        $heading4->appendChild(new Text('Test Heading'));

        $event = new DocumentParsedEvent($document);
        $processor($event);

        $headingLink1 = $heading1->firstChild();
        \assert($headingLink1 instanceof HeadingPermalink);
        $this->assertSame('test-heading', $headingLink1->getSlug());

        $headingLink2 = $heading2->firstChild();
        \assert($headingLink2 instanceof HeadingPermalink);
        $this->assertSame('test-heading-1', $headingLink2->getSlug());

        $headingLink3 = $heading3->firstChild();
        \assert($headingLink3 instanceof HeadingPermalink);
        $this->assertSame('test-heading-1-1', $headingLink3->getSlug());

        $headingLink4 = $heading4->firstChild();
        \assert($headingLink4 instanceof HeadingPermalink);
        $this->assertSame('test-heading-2', $headingLink4->getSlug());
    }

    /**
     * @param array<string, mixed> $values
     */
    private function createEnvironment(array $values = []): EnvironmentInterface
    {
        $environment = new Environment($values);
        $environment->addExtension(new HeadingPermalinkExtension());

        return $environment;
    }
}
