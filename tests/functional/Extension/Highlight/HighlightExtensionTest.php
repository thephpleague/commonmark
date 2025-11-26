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

namespace League\CommonMark\Tests\Functional\Extension\Highlight;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Highlight\HighlightExtension;
use League\CommonMark\Parser\MarkdownParser;
use League\CommonMark\Renderer\HtmlRenderer;
use PHPUnit\Framework\TestCase;

final class HighlightExtensionTest extends TestCase
{
    /**
     * @dataProvider dataForIntegrationTest
     */
    public function testMark(string $string, string $expected): void
    {
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new HighlightExtension());

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
            ['Hello, ==world!==', "<p>Hello, <mark>world!</mark></p>\n"],
            ['This is a test without any marks', "<p>This is a test without any marks</p>\n"],
            ['This is a test with ==valid== marks', "<p>This is a test with <mark>valid</mark> marks</p>\n"],
            ['This is a test `with` ==valid== marks', "<p>This is a test <code>with</code> <mark>valid</mark> marks</p>\n"],
            ['This is a ==unit== integration test', "<p>This is a <mark>unit</mark> integration test</p>\n"],
            ['==Mark== on the left', "<p><mark>Mark</mark> on the left</p>\n"],
            ['Mark on the ==right==', "<p>Mark on the <mark>right</mark></p>\n"],
            ['==Mark everywhere==', "<p><mark>Mark everywhere</mark></p>\n"],
            ['This ==test has no ending match', "<p>This ==test has no ending match</p>\n"],
            ['This ==test=== has mismatched equal signs', "<p>This ==test=== has mismatched equal signs</p>\n"],
            ['This ===test== also has mismatched equal signs', "<p>This ===test== also has mismatched equal signs</p>\n"],
            ['This one has ===three=== equal signs', "<p>This one has ===three=== equal signs</p>\n"],
            ["This ==has a\n\nnew paragraph==.", "<p>This ==has a</p>\n<p>new paragraph==.</p>\n"],
            ['Hello == == world', "<p>Hello == == world</p>\n"],
            ['This **is ==a little** test of mismatched delimiters==', "<p>This <strong>is ==a little</strong> test of mismatched delimiters==</p>\n"],
            ['Из: твоя ==тест== ветка', "<p>Из: твоя <mark>тест</mark> ветка</p>\n"],
            ['This one combines ==nested ==mark== text==', "<p>This one combines <mark>nested <mark>mark</mark> text</mark></p>\n"],
            ['Here we have **emphasized text containing a ==mark==**', "<p>Here we have <strong>emphasized text containing a <mark>mark</mark></strong></p>\n"],
            ['Four trailing equal signs ====', "<p>Four trailing equal signs ====</p>\n"],
            ['==Unmatched left', "<p>==Unmatched left</p>\n"],
            ['Unmatched right==', "<p>Unmatched right==</p>\n"],
            ['==foo=bar==', "<p><mark>foo=bar</mark></p>\n"],
            ['==foo==bar==', "<p><mark>foo</mark>bar==</p>\n"],
            ['==foo===bar==', "<p><mark>foo===bar</mark></p>\n"],
            ['==foo====bar==', "<p><mark>foo====bar</mark></p>\n"],
            ['==foo=====bar==', "<p><mark>foo=====bar</mark></p>\n"],
            ['==foo======bar==', "<p><mark>foo======bar</mark></p>\n"],
            ['==foo=======bar==', "<p><mark>foo=======bar</mark></p>\n"],
            ['> inside a ==blockquote==', "<blockquote>\n<p>inside a <mark>blockquote</mark></p>\n</blockquote>\n"],
        ];
    }
}
