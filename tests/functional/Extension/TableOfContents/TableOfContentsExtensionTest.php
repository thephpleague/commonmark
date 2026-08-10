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

namespace League\CommonMark\Tests\Functional\Extension\TableOfContents;

use League\CommonMark\ConverterInterface;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Event\DocumentPreRenderEvent;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\TableOfContents\Node\TableOfContents;
use League\CommonMark\Extension\TableOfContents\TableOfContentsExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Tests\Functional\AbstractLocalDataTestCase;

final class TableOfContentsExtensionTest extends AbstractLocalDataTestCase
{
    /**
     * @param array<string, mixed> $config
     */
    protected function createConverter(array $config = []): ConverterInterface
    {
        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new HeadingPermalinkExtension());
        $environment->addExtension(new TableOfContentsExtension());

        return new MarkdownConverter($environment);
    }

    /**
     * {@inheritDoc}
     */
    public static function dataProvider(): iterable
    {
        yield from self::loadTests(__DIR__ . '/md');
    }

    /**
     * @return array<string, mixed>
     */
    private static function placeholderConfig(): array
    {
        return ['table_of_contents' => ['position' => 'placeholder', 'placeholder' => '[TOC]']];
    }

    public function testDocumentContainsOneTableOfContentsRegardlessOfPlaceholderCount(): void
    {
        $environment = new Environment(self::placeholderConfig());
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new HeadingPermalinkExtension());
        $environment->addExtension(new TableOfContentsExtension());

        $found = 0;
        $environment->addEventListener(DocumentParsedEvent::class, static function (DocumentParsedEvent $event) use (&$found): void {
            foreach ($event->getDocument()->iterator() as $node) {
                if ($node instanceof TableOfContents) {
                    $found++;
                }
            }
        }, -200);

        $html = (new MarkdownConverter($environment))->convert("[TOC]\n\n# Alpha\n\n[TOC]\n\n# Bravo\n\n[TOC]\n")->getContent();

        $this->assertSame(1, $found);
        $this->assertSame(3, \substr_count($html, 'class="table-of-contents"'));
    }

    public function testTableOfContentsCanBeRepositionedBeforeRendering(): void
    {
        $environment = new Environment(self::placeholderConfig());
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new HeadingPermalinkExtension());
        $environment->addExtension(new TableOfContentsExtension());

        $environment->addEventListener(DocumentPreRenderEvent::class, static function (DocumentPreRenderEvent $event): void {
            foreach ($event->getDocument()->iterator() as $node) {
                if ($node instanceof TableOfContents) {
                    $node->detach();
                    $event->getDocument()->prependChild($node);

                    return;
                }
            }
        });

        $html = (new MarkdownConverter($environment))->convert("Intro.\n\n[TOC]\n\n# Alpha\n")->getContent();

        $this->assertLessThan(
            \strpos($html, '<p>Intro.</p>'),
            \strpos($html, 'class="table-of-contents"'),
            'The listener should have moved the table of contents above the intro paragraph',
        );
    }
}
