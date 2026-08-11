<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 * (c) 2015 Martin Hasoň <martin.hason@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\CommonMark\Tests\Unit\Extension\Attributes\Event;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Node\Block\Paragraph;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AttributesListenerTest extends TestCase
{
    /**
     * A class value another extension already placed on the node takes part in the merges, and one
     * holding no classes at all must leave the node without a class rather than with an empty one.
     *
     * @dataProvider dataForTestSeededClassWithoutAnyClasses
     *
     * @param mixed $seededClass
     */
    #[DataProvider('dataForTestSeededClassWithoutAnyClasses')]
    public function testSeededClassWithoutAnyClasses(string $markdown, $seededClass, string $expected): void
    {
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addEventListener(DocumentParsedEvent::class, static function (DocumentParsedEvent $event) use ($seededClass): void {
            foreach ($event->getDocument()->iterator() as $node) {
                if ($node instanceof Paragraph) {
                    $node->data->set('attributes', ['class' => $seededClass, 'data-x' => 'y']);
                }
            }
        }, 100);
        $environment->addExtension(new AttributesExtension());

        $this->assertSame($expected, \trim((new MarkdownConverter($environment))->convert($markdown)->getContent()));
    }

    /**
     * @return iterable<array{string, mixed, string}>
     */
    public static function dataForTestSeededClassWithoutAnyClasses(): iterable
    {
        foreach (['empty string' => '', 'blank string' => '  ', 'empty array' => []] as $label => $class) {
            yield $label . ', nothing else contributes a class' => ['para {#z}', $class, '<p data-x="y" id="z">para</p>'];
            yield $label . ', a later attribute contributes one' => ['para {.c}', $class, '<p data-x="y" class="c">para</p>'];
            yield $label . ', an earlier attribute contributes one' => ["{#i}\npara", $class, '<p id="i" data-x="y">para</p>'];
        }

        // A value which is not a string or array still contributes to the list, and so does create it
        yield 'zero contributes a class' => ['para {#z}', 0, '<p class="0" data-x="y" id="z">para</p>'];
    }
}
