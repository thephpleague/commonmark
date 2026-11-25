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

namespace League\CommonMark\Tests\Functional\Extension\Attributes;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;
use League\CommonMark\Extension\Footnote\FootnoteExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Browsers keep only the first of two attributes whose names differ in case, and user attributes
 * are rendered before those added by renderers and extensions - so a mixed-case name must never
 * survive alongside its lowercase counterpart.
 *
 * @internal
 */
final class AttributeNameCaseTest extends TestCase
{
    private const EXTERNAL_LINK_CONFIG = [
        'external_link' => [
            'internal_hosts' => ['example.com'],
            'open_in_new_window' => true,
            'nofollow' => 'external',
            'noopener' => 'external',
            'noreferrer' => 'external',
        ],
    ];

    /**
     * @dataProvider dataForTestAttributeNameCase
     *
     * @param array<string, mixed>     $config
     * @param list<ExtensionInterface> $extensions
     */
    #[DataProvider('dataForTestAttributeNameCase')]
    public function testAttributeNameCase(string $markdown, string $expected, array $config = [], array $extensions = []): void
    {
        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new AttributesExtension());
        foreach ($extensions as $extension) {
            $environment->addExtension($extension);
        }

        $actual = \trim((string) (new MarkdownConverter($environment))->convert($markdown));

        $this->assertSame($expected, $actual);
    }

    /**
     * @return iterable<string, array<mixed>>
     */
    public static function dataForTestAttributeNameCase(): iterable
    {
        yield 'rel merges with external link rel' => [
            '[l](http://ext.com){REL=opener}',
            '<p><a rel="opener nofollow noopener noreferrer" target="_blank" href="http://ext.com">l</a></p>',
            self::EXTERNAL_LINK_CONFIG,
            [new ExternalLinkExtension()],
        ];

        yield 'lowercase rel is unchanged' => [
            '[l](http://ext.com){rel=opener}',
            '<p><a rel="opener nofollow noopener noreferrer" target="_blank" href="http://ext.com">l</a></p>',
            self::EXTERNAL_LINK_CONFIG,
            [new ExternalLinkExtension()],
        ];

        yield 'target cannot override external link target' => [
            '[l](http://ext.com){TARGET=_self}',
            '<p><a target="_blank" rel="nofollow noopener noreferrer" href="http://ext.com">l</a></p>',
            self::EXTERNAL_LINK_CONFIG,
            [new ExternalLinkExtension()],
        ];

        yield 'href cannot override link destination' => [
            '[l](http://ok.com){HREF="javascript:alert(1)"}',
            '<p><a href="http://ok.com">l</a></p>',
        ];

        yield 'safe href cannot override link destination' => [
            '[l](http://ok.com){HREF="http://evil.com"}',
            '<p><a href="http://ok.com">l</a></p>',
            ['allow_unsafe_links' => false],
        ];

        yield 'src cannot override image source' => [
            '![i](http://ok.com/a.png){SRC="http://evil.com/b.png"}',
            '<p><img src="http://ok.com/a.png" alt="i" /></p>',
        ];

        yield 'href cannot override footnote reference' => [
            "x[^1]{HREF=http://evil.com}\n\n[^1]: note",
            "<p>x<sup id=\"fnref:1\"><a href=\"#fn:1\" class=\"footnote-ref\" role=\"doc-noteref\">1</a></sup></p>\n<div class=\"footnotes\" role=\"doc-endnotes\"><hr /><ol><li class=\"footnote\" id=\"fn:1\" role=\"doc-endnote\"><p>note&nbsp;<a class=\"footnote-backref\" rev=\"footnote\" href=\"#fnref:1\" role=\"doc-backlink\">↩</a></p></li></ol></div>",
            [],
            [new FootnoteExtension()],
        ];

        yield 'start cannot override ordered list start' => [
            "{START=7}\n3. a",
            "<ol start=\"3\">\n<li>a</li>\n</ol>",
        ];

        yield 'heading id is handled like lowercase id' => [
            '# H {ID=x}',
            '<h1 id="content-x"><a href="#content-x" class="heading-permalink" aria-hidden="true" tabindex="-1" title="Permalink">¶</a>H</h1>',
            ['heading_permalink' => ['apply_id_to_heading' => true]],
            [new HeadingPermalinkExtension()],
        ];

        yield 'heading id matches lowercase behavior' => [
            '# H {id=x}',
            '<h1 id="content-x"><a href="#content-x" class="heading-permalink" aria-hidden="true" tabindex="-1" title="Permalink">¶</a>H</h1>',
            ['heading_permalink' => ['apply_id_to_heading' => true]],
            [new HeadingPermalinkExtension()],
        ];

        yield 'id then #id: last one wins' => ['x {ID=x #a}', '<p id="a">x</p>'];
        yield '#id then id: last one wins' => ['x {#a ID=x}', '<p id="x">x</p>'];
        yield 'title variants dedupe' => ['x {Title=a title=b}', '<p title="b">x</p>'];

        yield 'block attributes are lowercased' => [
            "{Title=a}\n{TITLE=b .c}\nx",
            '<p class="c" title="b">x</p>',
        ];

        yield 'align cannot override table cell alignment' => [
            "| a {ALIGN=right} |\n|:-|\n| b |",
            "<table>\n<thead>\n<tr>\n<th align=\"left\">a</th>\n</tr>\n</thead>\n<tbody>\n<tr>\n<td align=\"left\">b</td>\n</tr>\n</tbody>\n</table>",
            [],
            [new TableExtension()],
        ];

        yield 'allow list entries are case-insensitive' => [
            'x {.c #i title=t}',
            '<p class="c" id="i">x</p>',
            ['attributes' => ['allow' => ['CLASS', 'ID']]],
        ];

        yield 'mixed case input matches lowercase allow list' => [
            'x {Data-Foo=1 ID=y TITLE=t}',
            '<p data-foo="1" id="y">x</p>',
            ['attributes' => ['allow' => ['data-foo', 'id']]],
        ];

        yield 'event handlers are still dropped with an allow list' => [
            'x {ONCLICK=a id=b}',
            '<p id="b">x</p>',
            ['attributes' => ['allow' => ['id', 'Class']]],
        ];
    }
}
