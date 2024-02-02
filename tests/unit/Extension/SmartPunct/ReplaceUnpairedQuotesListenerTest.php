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

namespace League\CommonMark\Tests\Unit\Extension\SmartPunct;

use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Inline\Strong;
use League\CommonMark\Extension\SmartPunct\Quote;
use League\CommonMark\Extension\SmartPunct\ReplaceUnpairedQuotesListener;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Node\Node;
use PHPUnit\Framework\TestCase;

final class ReplaceUnpairedQuotesListenerTest extends TestCase
{
    /**
     * @dataProvider provideTestData
     *
     * @param array<int, Node> $paragraphNodes
     */
    public function testWithConsecutiveMerging(array $paragraphNodes, string $expectedText): void
    {
        $document = new Document();
        $document->appendChild($paragraph = new Paragraph());
        $paragraph->replaceChildren($paragraphNodes);

        (new ReplaceUnpairedQuotesListener())(new DocumentParsedEvent($document));

        $this->assertCount(1, $paragraph->children());
        $this->assertInstanceOf(Text::class, $paragraph->firstChild());
        $this->assertSame($expectedText, $paragraph->firstChild()->getLiteral());
    }

    /**
     * @return iterable<mixed>
     */
    public static function provideTestData(): iterable
    {
        yield [
            [
                new Text('Don'),
                new Quote('\''),
                new Text('t you just love CommonMark?'),
            ],
            'Don’t you just love CommonMark?',
        ];

        yield [
            [
                new Quote('\''),
                new Text('tis the season to be jolly'),
            ],
            '’tis the season to be jolly',
        ];

        yield [
            [
                new Quote('"'),
                new Text('A paragraph with no closing quote.'),
            ],
            '“A paragraph with no closing quote.',
        ];

        yield [
            [
                new Quote('“'),
                new Text('A paragraph with no closing quote.'),
            ],
            '“A paragraph with no closing quote.',
        ];
    }

    public function testWhenMergingNotPossible(): void
    {
        $document = new Document();
        $document->appendChild($paragraph = new Paragraph());

        $strong = new Strong();
        $strong->appendChild(new Text('This does not get merged'));

        $paragraph->replaceChildren([
            new Quote('"'),
            $strong,
        ]);

        (new ReplaceUnpairedQuotesListener())(new DocumentParsedEvent($document));

        $this->assertCount(2, $paragraph->children());
        $this->assertInstanceOf(Text::class, $paragraph->firstChild());
        $this->assertSame('“', $paragraph->firstChild()->getLiteral());
        $this->assertInstanceOf(Strong::class, $paragraph->lastChild());
    }
}
