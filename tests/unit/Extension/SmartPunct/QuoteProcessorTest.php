<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Extension\SmartPunct;

use League\CommonMark\Delimiter\DelimiterInterface;
use League\CommonMark\Extension\SmartPunct\QuoteProcessor;
use League\CommonMark\Node\Inline\Text;
use PHPUnit\Framework\TestCase;

/**
 * Tests the quote processor
 */
final class QuoteProcessorTest extends TestCase
{
    public function testSingleQuoteProcessor(): void
    {
        $mockDelimiter = $this->createMock(DelimiterInterface::class);

        $processor = QuoteProcessor::createSingleQuoteProcessor();

        $this->assertEquals("'", $processor->getOpeningCharacter());
        $this->assertEquals("'", $processor->getClosingCharacter());
        $this->assertEquals(1, $processor->getMinLength());
        $this->assertEquals(1, $processor->getDelimiterUse($mockDelimiter, $mockDelimiter));

        $opener = new Text();
        $closer = new Text();

        $processor->process($opener, $closer, 1);

        $this->assertEquals('‘', $opener->next()->getLiteral());
        $this->assertEquals('’', $closer->previous()->getLiteral());
    }

    public function testDoubleQuoteProcessor(): void
    {
        $mockDelimiter = $this->createMock(DelimiterInterface::class);

        $processor = QuoteProcessor::createDoubleQuoteProcessor();

        $this->assertEquals('"', $processor->getOpeningCharacter());
        $this->assertEquals('"', $processor->getClosingCharacter());
        $this->assertEquals(1, $processor->getMinLength());
        $this->assertEquals(1, $processor->getDelimiterUse($mockDelimiter, $mockDelimiter));

        $opener = new Text();
        $closer = new Text();

        $processor->process($opener, $closer, 1);

        $this->assertEquals('“', $opener->next()->getLiteral());
        $this->assertEquals('”', $closer->previous()->getLiteral());
    }
}
