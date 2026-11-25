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

use League\CommonMark\Extension\SmartPunct\DashParser;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\InlineParserContext;
use League\CommonMark\Reference\ReferenceMapInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class DashParserTest extends TestCase
{
    /**
     * @dataProvider dataForTestParse
     */
    #[DataProvider('dataForTestParse')]
    public function testParse(string $string, int $start, ?string $expected): void
    {
        $paragraph = new Paragraph();
        $cursor    = new Cursor($string);
        $context   = new InlineParserContext($cursor, $paragraph, $this->createMock(ReferenceMapInterface::class));
        $cursor->advanceBy($start);

        $parsed = (new DashParser())->parse($context->withMatches(['-']));

        if ($expected === null) {
            $this->assertFalse($parsed);
            $this->assertSame($start, $cursor->getPosition());
            $this->assertFalse($paragraph->hasChildren());

            return;
        }

        $this->assertTrue($parsed);
        $text = $paragraph->firstChild();
        $this->assertInstanceOf(Text::class, $text);
        $this->assertSame($expected, $text->getLiteral());
        $this->assertTrue($cursor->isAtEnd());
    }

    /**
     * @return iterable<array{string, int, ?string}>
     */
    public static function dataForTestParse(): iterable
    {
        yield 'whole run' => ['a---', 1, '—'];
        yield 'rest of a run whose first hyphen was escaped' => ['\\---', 2, '–'];
        yield 'single hyphen' => ['a-', 1, null];
        yield 'not at a hyphen' => ['a--', 0, null];
    }
}
