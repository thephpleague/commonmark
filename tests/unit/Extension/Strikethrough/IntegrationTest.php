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

namespace League\CommonMark\Tests\Unit\Extension\Strikethrough;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\Parser\MarkdownParser;
use League\CommonMark\Renderer\HtmlRenderer;
use PHPUnit\Framework\TestCase;

final class IntegrationTest extends TestCase
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
    public function dataForIntegrationTest(): array
    {
        return [
            ['This is a test without any strikethroughs', "<p>This is a test without any strikethroughs</p>\n"],
            ['This is a test without any ~valid~ strikethroughs', "<p>This is a test without any ~valid~ strikethroughs</p>\n"],
            ['This is a test `without` any ~valid~ strikethroughs', "<p>This is a test <code>without</code> any ~valid~ strikethroughs</p>\n"],
            ['This is a ~~unit~~ integration test', "<p>This is a <del>unit</del> integration test</p>\n"],
            ['~~Strikethrough~~ on the left', "<p><del>Strikethrough</del> on the left</p>\n"],
            ['Strikethrough on the ~~right~~', "<p>Strikethrough on the <del>right</del></p>\n"],
            ['~~Strikethrough everywhere~~', "<p><del>Strikethrough everywhere</del></p>\n"],
            ['This ~~test has no ending match', "<p>This ~~test has no ending match</p>\n"],
            ['This ~~test~~~ has mismatched tildes', "<p>This <del>test</del>~ has mismatched tildes</p>\n"],
            ['This ~~~test~~ also has mismatched tildes', "<p>This ~<del>test</del> also has mismatched tildes</p>\n"],
            ['This one has ~~~three~~~ tildes', "<p>This one has <del>three</del> tildes</p>\n"],
            ["This ~~has a\n\nnew paragraph~~.", "<p>This ~~has a</p>\n<p>new paragraph~~.</p>\n"],
            ['Hello ~~ ~~ world', "<p>Hello ~~ ~~ world</p>\n"],
            ['This **is ~~a little** test of mismatched delimiters~~', "<p>This <strong>is ~~a little</strong> test of mismatched delimiters~~</p>\n"],
            ['Из: твоя ~~тест~~ ветка', "<p>Из: твоя <del>тест</del> ветка</p>\n"],
            ['This one combines ~~nested ~~strikethrough~~ text~~', "<p>This one combines <del>nested <del>strikethrough</del> text</del></p>\n"],
            ['Here we have **emphasized text containing a ~~strikethrough~~**', "<p>Here we have <strong>emphasized text containing a <del>strikethrough</del></strong></p>\n"],
        ];
    }
}
