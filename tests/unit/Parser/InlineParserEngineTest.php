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

namespace League\CommonMark\Tests\Unit\Parser;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserEngine;
use League\CommonMark\Reference\ReferenceMap;
use League\CommonMark\Tests\Unit\Parser\Inline\FakeInlineParser;
use PHPUnit\Framework\TestCase;

final class InlineParserEngineTest extends TestCase
{
    public function testParseWithDefaultPriorityOrder(): void
    {
        $colorParser      = new FakeInlineParser(InlineParserMatch::string('brown'));
        $adjectiveParser  = new FakeInlineParser(InlineParserMatch::oneOf('quick', 'brown', 'lazy'));
        $fiveLetterParser = new FakeInlineParser(InlineParserMatch::regex('\b\w{5}\b'));

        $environment = new Environment();
        $environment->addInlineParser($colorParser);
        $environment->addInlineParser($adjectiveParser);
        $environment->addInlineParser($fiveLetterParser);

        $engine    = new InlineParserEngine($environment, new ReferenceMap());
        $paragraph = new Paragraph();
        $engine->parse('The quick brown fox jumps over the lazy dog', $paragraph);

        $this->assertSame(['brown'], $colorParser->getMatches());
        $this->assertSame(['quick', 'lazy'], $adjectiveParser->getMatches());
        $this->assertSame(['jumps'], $fiveLetterParser->getMatches());
    }

    public function testParseWithDifferentPriorityOrder(): void
    {
        $colorParser      = new FakeInlineParser(InlineParserMatch::string('brown'));
        $adjectiveParser  = new FakeInlineParser(InlineParserMatch::oneOf('quick', 'brown', 'lazy'));
        $fiveLetterParser = new FakeInlineParser(InlineParserMatch::regex('\b\w{5}\b'));

        $environment = new Environment();
        $environment->addInlineParser($colorParser, 100);
        $environment->addInlineParser($adjectiveParser, -100);
        $environment->addInlineParser($fiveLetterParser);

        $engine    = new InlineParserEngine($environment, new ReferenceMap());
        $paragraph = new Paragraph();
        $engine->parse('The quick brown fox jumps over the lazy dog', $paragraph);

        $this->assertSame(['brown'], $colorParser->getMatches());
        $this->assertSame(['lazy'], $adjectiveParser->getMatches());
        $this->assertSame(['quick', 'jumps'], $fiveLetterParser->getMatches());
    }
}
