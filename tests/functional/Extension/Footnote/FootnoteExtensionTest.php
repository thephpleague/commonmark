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

namespace League\CommonMark\Tests\Functional\Extension\Footnote;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Footnote\FootnoteExtension;
use League\CommonMark\MarkdownConverter;
use PHPUnit\Framework\TestCase;

final class FootnoteExtensionTest extends TestCase
{
    /**
     * @dataProvider dataForIntegrationTest
     *
     * @param array<string, mixed> $config
     */
    public function testFootnote(string $string, string $expected, array $config = []): void
    {
        $environment = new Environment(['footnote' => $config]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new FootnoteExtension());

        $converter = new MarkdownConverter($environment);

        $html = \trim((string) $converter->convert($string));

        $this->assertSame($expected, $html);
    }

    /**
     * @return array<array<string>>
     */
    public static function dataForIntegrationTest(): array
    {
        return [
            [
                "Here[^note1]\n\n[^note1]: There",
                '<p>Here<sup id="fnref:note1"><a class="footnote-ref" href="#fn:note1" role="doc-noteref">1</a></sup></p>
<div class="footnotes" role="doc-endnotes"><hr /><ol><li class="footnote" id="fn:note1" role="doc-endnote"><p>There&nbsp;<a class="footnote-backref" rev="footnote" href="#fnref:note1" role="doc-backlink">↩</a></p></li></ol></div>',
            ],
            [
                "Here[^note1]\n\n[^note1]: There",
                '<p>Here<sup id="customfnref:note1"><a class="footnote-ref" href="#customfn:note1" role="doc-noteref">1</a></sup></p>
<div class="footnotes" role="doc-endnotes"><hr /><ol><li class="footnote" id="customfn:note1" role="doc-endnote"><p>There&nbsp;<a class="footnote-backref" rev="footnote" href="#customfnref:note1" role="doc-backlink">↩</a></p></li></ol></div>',
                ['ref_id_prefix' => 'customfnref:', 'footnote_id_prefix' => 'customfn:'],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTestFootnotesWithCustomOptions
     */
    public function testFootnotesWithCustomOptions(string $input, string $expected): void
    {
        $environment = new Environment([
            'footnote' => [
                'backref_class'      => 'custom-backref',
                // Ensure multiple characters are allowed (including multibyte) and special HTML characters are escaped.
                'backref_symbol'     => '↩ 🦄️ <3 You',
                'container_add_hr'   => false,
                'container_class'    => 'custom-notes',
                'ref_class'          => 'custom-ref',
                'ref_id_prefix'      => 'fnref:',
                'footnote_class'     => 'custom-footnote',
                'footnote_id_prefix' => 'fn:',
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new FootnoteExtension());

        $converter = new MarkdownConverter($environment);

        $this->assertEquals($expected, \trim((string) $converter->convert($input)));
    }

    public static function dataProviderForTestFootnotesWithCustomOptions(): \Generator
    {
        yield ["Here[^note1]\n\n[^note1]: There", '<p>Here<sup id="fnref:note1"><a class="custom-ref" href="#fn:note1" role="doc-noteref">1</a></sup></p>' . "\n" . '<div class="custom-notes" role="doc-endnotes"><ol><li class="custom-footnote" id="fn:note1" role="doc-endnote"><p>There&nbsp;<a class="custom-backref" rev="footnote" href="#fnref:note1" role="doc-backlink">↩ 🦄️ &lt;3 You</a></p></li></ol></div>'];
        yield ["_Here_[^note1]\n\n[^note1]: **There**", '<p><em>Here</em><sup id="fnref:note1"><a class="custom-ref" href="#fn:note1" role="doc-noteref">1</a></sup></p>' . "\n" . '<div class="custom-notes" role="doc-endnotes"><ol><li class="custom-footnote" id="fn:note1" role="doc-endnote"><p><strong>There</strong>&nbsp;<a class="custom-backref" rev="footnote" href="#fnref:note1" role="doc-backlink">↩ 🦄️ &lt;3 You</a></p></li></ol></div>'];
    }

    public function testFootnotesWithEmptySymbol(): void
    {
        $environment = new Environment([
            'footnote' => [
                'backref_symbol' => '',
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new FootnoteExtension());

        $converter = new MarkdownConverter($environment);

        $input    = "Here[^note1]\n\n[^note1]: There";
        $expected = '<p>Here<sup id="fnref:note1"><a class="footnote-ref" href="#fn:note1" role="doc-noteref">1</a></sup></p>' . "\n" . '<div class="footnotes" role="doc-endnotes"><hr /><ol><li class="footnote" id="fn:note1" role="doc-endnote"><p>There&nbsp;<a class="footnote-backref" rev="footnote" href="#fnref:note1" role="doc-backlink" /></p></li></ol></div>';

        $this->assertEquals($expected, \trim((string) $converter->convert($input)));
    }
}
