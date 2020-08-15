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

namespace League\CommonMark\Tests\Unit\Extension\Footnote;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\Footnote\FootnoteExtension;
use PHPUnit\Framework\TestCase;

class IntegrationTest extends TestCase
{
    /**
     * @dataProvider dataForIntegrationTest
     */
    public function testFootnote(string $string, string $expected, array $config = []): void
    {
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new FootnoteExtension([
            'ref_id_prefix'      => 'custom-ref-',
            'footnote_id_prefix' => 'custom-',
        ]));

        $converter = new CommonMarkConverter(['footnote' => $config], $environment);

        $html = \trim($converter->convertToHtml($string));

        $this->assertSame($expected, $html);
    }

    /**
     * @return array<array<string>>
     */
    public function dataForIntegrationTest(): array
    {
        return [
            [
                "Here[^note1]\n\n[^note1]: There",
                '<p>Here<sup id="fnref:note1"><a class="footnote-ref" href="#fn:note1" role="doc-noteref">1</a></sup></p>
<div class="footnotes" role="doc-endnotes"><hr /><ol><li class="footnote" id="fn:note1" role="doc-endnote"><p>There&nbsp;<a class="footnote-backref" rev="footnote" href="#fnref:note1" role="doc-backlink">&#8617;</a></p></li></ol></div>',
            ],
            [
                "Here[^note1]\n\n[^note1]: There",
                '<p>Here<sup id="customfnref:note1"><a class="footnote-ref" href="#customfn:note1" role="doc-noteref">1</a></sup></p>
<div class="footnotes" role="doc-endnotes"><hr /><ol><li class="footnote" id="customfn:note1" role="doc-endnote"><p>There&nbsp;<a class="footnote-backref" rev="footnote" href="#customfnref:note1" role="doc-backlink">&#8617;</a></p></li></ol></div>',
                ['ref_id_prefix' => 'customfnref:', 'footnote_id_prefix' => 'customfn:'],
            ],
        ];
    }
}
