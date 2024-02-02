<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com> and uAfrica.com (http://uafrica.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Functional\Extension\Strikethrough;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\Parser\MarkdownParser;
use League\CommonMark\Renderer\HtmlRenderer;
use PHPUnit\Framework\TestCase;

final class StrikethroughExtensionTest extends TestCase
{
    /**
     * @dataProvider dataForIntegrationTest
     */
    public function testStrikethrough(string $string, string $expected): void
    {
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new StrikethroughExtension());

        $parser   = new MarkdownParser($environment);
        $renderer = new HtmlRenderer($environment);

        $document = $parser->parse($string);

        $html = (string) $renderer->renderDocument($document);

        $this->assertSame($expected, $html);
    }

    /**
     * @return array<array<string>>
     */
    public static function dataForIntegrationTest(): array
    {
        return [
            ['~~Hi~~ Hello, ~there~ world!', "<p><del>Hi</del> Hello, <del>there</del> world!</p>\n"],
            ['This is a test without any strikethroughs', "<p>This is a test without any strikethroughs</p>\n"],
            ['This is a test with ~valid~ strikethroughs', "<p>This is a test with <del>valid</del> strikethroughs</p>\n"],
            ['This is a test `with` ~valid~ strikethroughs', "<p>This is a test <code>with</code> <del>valid</del> strikethroughs</p>\n"],
            ['This is a ~~unit~~ integration test', "<p>This is a <del>unit</del> integration test</p>\n"],
            ['~~Strikethrough~~ on the left', "<p><del>Strikethrough</del> on the left</p>\n"],
            ['Strikethrough on the ~~right~~', "<p>Strikethrough on the <del>right</del></p>\n"],
            ['~~Strikethrough everywhere~~', "<p><del>Strikethrough everywhere</del></p>\n"],
            ['This ~~test has no ending match', "<p>This ~~test has no ending match</p>\n"],
            ['This ~~test~~~ has mismatched tildes', "<p>This ~~test~~~ has mismatched tildes</p>\n"],
            ['This ~~~test~~ also has mismatched tildes', "<p>This ~~~test~~ also has mismatched tildes</p>\n"],
            ['This one has ~~~three~~~ tildes', "<p>This one has ~~~three~~~ tildes</p>\n"],
            ["This ~~has a\n\nnew paragraph~~.", "<p>This ~~has a</p>\n<p>new paragraph~~.</p>\n"],
            ['Hello ~~ ~~ world', "<p>Hello ~~ ~~ world</p>\n"],
            ['This **is ~~a little** test of mismatched delimiters~~', "<p>This <strong>is ~~a little</strong> test of mismatched delimiters~~</p>\n"],
            ['Из: твоя ~~тест~~ ветка', "<p>Из: твоя <del>тест</del> ветка</p>\n"],
            ['This one combines ~~nested ~~strikethrough~~ text~~', "<p>This one combines <del>nested <del>strikethrough</del> text</del></p>\n"],
            ['Here we have **emphasized text containing a ~~strikethrough~~**', "<p>Here we have <strong>emphasized text containing a <del>strikethrough</del></strong></p>\n"],
            ['Four trailing tildes ~~~~', "<p>Four trailing tildes ~~~~</p>\n"],
            ['~~Unmatched left', "<p>~~Unmatched left</p>\n"],
            ['Unmatched right~~', "<p>Unmatched right~~</p>\n"],
            ['~~foo~bar~~', "<p><del>foo~bar</del></p>\n"],
            ['~~foo~~bar~~', "<p><del>foo</del>bar~~</p>\n"],
            ['~~foo~~~bar~~', "<p><del>foo~~~bar</del></p>\n"],
            ['~~foo~~~~bar~~', "<p><del>foo~~~~bar</del></p>\n"],
            ['~~foo~~~~~bar~~', "<p><del>foo~~~~~bar</del></p>\n"],
            ['~~foo~~~~~~bar~~', "<p><del>foo~~~~~~bar</del></p>\n"],
            ['~~foo~~~~~~~bar~~', "<p><del>foo~~~~~~~bar</del></p>\n"],
            ['> inside a ~~blockquote~~', "<blockquote>\n<p>inside a <del>blockquote</del></p>\n</blockquote>\n"],
        ];
    }
}
