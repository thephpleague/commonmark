<?php

declare(strict_types=1);


namespace League\CommonMark\Tests\Unit\Extension\Marker;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Marker\MarkerExtension;
use League\CommonMark\Parser\MarkdownParser;
use League\CommonMark\Renderer\HtmlRenderer;
use PHPUnit\Framework\TestCase;

final class MarkerIntegrationTest extends TestCase
{
    /**
     * @dataProvider dataForIntegrationTest
     */
    public function testMarker(string $string, string $expected): void
    {
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new MarkerExtension());

        $parser = new MarkdownParser($environment);
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
            ['This is a test without any markers', "<p>This is a test without any markers</p>\n"],
            ['This is a test without any =valid= markers', "<p>This is a test without any =valid= markers</p>\n"],
            ['This is a test `without` any =valid= markers', "<p>This is a test <code>without</code> any =valid= markers</p>\n"],
            ['This is a ==unit== integration test', "<p>This is a <mark>unit<mark> integration test</p>\n"],
            ['==Marker== on the left', "<p><mark>Marker<mark> on the left</p>\n"],
            ['Marker on the ==right==', "<p>Marker on the <mark>right<mark></p>\n"],
            ['==Marker everywhere==', "<p><mark>Marker everywhere<mark></p>\n"],
            ['This ==test has no ending match', "<p>This ==test has no ending match</p>\n"],
            ['This ==test=== has mismatched equal signs', "<p>This <mark>test<mark>= has mismatched equal signs</p>\n"],
            ['This ===test== also has mismatched equal signs', "<p>This =<mark>test<mark> also has mismatched equal signs</p>\n"],
            ['This one has ===three=== equal signs', "<p>This one has <mark>three<mark> equal signs</p>\n"],
            ["This ==has a\n\nnew paragraph==.", "<p>This ==has a</p>\n<p>new paragraph==.</p>\n"],
            ['Hello == == world', "<p>Hello == == world</p>\n"],
            ['This **is ==a little** test of mismatched delimiters==', "<p>This <strong>is ==a little</strong> test of mismatched delimiters==</p>\n"],
            ['Из: твоя ==тест== ветка', "<p>Из: твоя <mark>тест<mark> ветка</p>\n"],
            ['This one combines ==nested ==marker== text==', "<p>This one combines <mark>nested <mark>marker<mark> text<mark></p>\n"],
            ['Here we have **emphasized text containing a ==marker==**', "<p>Here we have <strong>emphasized text containing a <mark>marker<mark></strong></p>\n"],
        ];
    }
}