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
     */
    public function testExamples(string $input, $expectedFrontMatter, string $expectedContent): void
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
        ];

        yield [
            "---\nJust a string\n---\nYay\n---",
            'Just a string',
            "Yay\n---",
        ];

        yield [
            "Hello World!\n---",
            null,
            "Hello World!\n---",
        ];

        yield [
            "---\nThis is a heading\n-----------------",
            null,
            "---\nThis is a heading\n-----------------",
        ];

        yield [
            "---\nfront_matter_only: true\n---\n",
            [
                'front_matter_only' => true,
            ],
            '',
        ];

        yield [
            "---\nfront_matter_only: true\n---\n\n\n\n\n",
            [
                'front_matter_only' => true,
            ],
            '',
        ];

        yield [
            "\n---\ninvalid_because: front matter must be first\n---",
            null,
            "\n---\ninvalid_because: front matter must be first\n---",
        ];

        yield [
            "---\nMissing the closer",
            null,
            "---\nMissing the closer",
        ];

        yield [
            "---\ndelimiter: ---inside\n---\ntest",
            [
                'delimiter' => '---inside',
            ],
            'test',
        ];

        yield [
            "---\ninvalid: closer\n--- can't have text here\n",
            null,
            "---\ninvalid: closer\n--- can't have text here\n",
        ];

        yield [
            "---\n---\nInvalid front matter",
            null,
            "---\n---\nInvalid front matter",
        ];

        yield [
            "---\n\n---\nEmpty front matter",
            null,
            'Empty front matter',
        ];
    }
}
