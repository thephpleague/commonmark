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

namespace League\CommonMark\Tests\Unit\Extension\FrontMatter;

use League\CommonMark\Event\DocumentPreParsedEvent;
use League\CommonMark\Extension\FrontMatter\FrontMatterParserListener;
use League\CommonMark\Extension\FrontMatter\Yaml\SymfonyFrontMatterParser;
use League\CommonMark\Input\MarkdownInput;
use League\CommonMark\Node\Block\Document;
use PHPUnit\Framework\TestCase;

final class FrontMatterParserListenerTest extends TestCase
{
    /**
     * @dataProvider dataForTestExamples
     *
     * @param string $input               The raw Markdown document input
     * @param mixed  $expectedFrontMatter What the front matter should contain
     * @param string $expectedContent     What the Markdown (less the front matter) should be
     * @param int    $expectedOffset      The 0-based starting line of the resulting Markdown (less the front matter)
     */
    public function testExamples(string $input, $expectedFrontMatter, string $expectedContent, int $expectedOffset): void
    {
        $document = new Document();
        $markdown = new MarkdownInput($input);
        $parser   = new FrontMatterParserListener(new SymfonyFrontMatterParser());

        $event = new DocumentPreParsedEvent($document, $markdown);
        $parser->__invoke($event);

        $this->assertSame($expectedFrontMatter, $document->getData('front_matter'));
        $this->assertSame($expectedContent, $event->getMarkdown()->getContent());

        $this->assertSame($expectedOffset, $event->getMarkdown()->getLineOffset());
    }

    /**
     * @return iterable<mixed>
     */
    public function dataForTestExamples(): iterable
    {
        yield [
            "---\ntitle: Hello World!\npublished: true\n---\nYay\n---",
            [
                'title' => 'Hello World!',
                'published' => true,
            ],
            "Yay\n---",
            4,
        ];

        yield [
            "---\nJust a string\n---\nYay\n---",
            'Just a string',
            "Yay\n---",
            3,
        ];

        yield [
            "Hello World!\n---",
            null,
            "Hello World!\n---",
            0,
        ];

        yield [
            "---\nThis is a heading\n-----------------",
            null,
            "---\nThis is a heading\n-----------------",
            0,
        ];

        yield [
            "---\nfront_matter_only: true\n---\n",
            [
                'front_matter_only' => true,
            ],
            '',
            3,
        ];

        yield [
            "---\nfront_matter_only: true\n---\n\n\n\n\n",
            [
                'front_matter_only' => true,
            ],
            '',
            7,
        ];

        yield [
            "\n---\ninvalid_because: front matter must be first\n---",
            null,
            "\n---\ninvalid_because: front matter must be first\n---",
            0,
        ];

        yield [
            "---\nMissing the closer",
            null,
            "---\nMissing the closer",
            0,
        ];

        yield [
            "---\ndelimiter: ---inside\n---\ntest",
            [
                'delimiter' => '---inside',
            ],
            'test',
            3,
        ];

        yield [
            "---\ninvalid: closer\n--- can't have text here\n",
            null,
            "---\ninvalid: closer\n--- can't have text here\n",
            0,
        ];

        yield [
            "---\n---\nInvalid front matter",
            null,
            "---\n---\nInvalid front matter",
            0,
        ];

        yield [
            "---\n\n---\nEmpty front matter",
            null,
            'Empty front matter',
            3,
        ];
    }
}
