<?php

/*
 * This file is part of the league/commonmark-ext-smartpunct package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Ext\SmartPunct\Tests;

use League\CommonMark\Delimiter\Delimiter;
use League\CommonMark\Ext\SmartPunct\QuoteProcessor;
use League\CommonMark\Inline\Element\Text;
use PHPUnit\Framework\TestCase;

/**
 * Tests the quote processor
 */
final class QuoteProcessorTest extends TestCase
{
    public function testSingleQuoteProcessor()
    {
        $mockDelimiter = $this->createMock(Delimiter::class);

        $processor = QuoteProcessor::createSingleQuoteProcessor();

        $this->assertEquals("'", $processor->getOpeningCharacter());
        $this->assertEquals("'", $processor->getClosingCharacter());
        $this->assertEquals(1, $processor->getMinLength());
        $this->assertEquals(1, $processor->getDelimiterUse($mockDelimiter, $mockDelimiter));

        $opener = new Text();
        $closer = new Text();

        $processor->process($opener, $closer, 1);

        $this->assertEquals('‘', $opener->next()->getContent());
        $this->assertEquals('’', $closer->previous()->getContent());
    }

    public function testDoubleQuoteProcessor()
    {
        $mockDelimiter = $this->createMock(Delimiter::class);

        $processor = QuoteProcessor::createDoubleQuoteProcessor();

        $this->assertEquals('"', $processor->getOpeningCharacter());
        $this->assertEquals('"', $processor->getClosingCharacter());
        $this->assertEquals(1, $processor->getMinLength());
        $this->assertEquals(1, $processor->getDelimiterUse($mockDelimiter, $mockDelimiter));

        $opener = new Text();
        $closer = new Text();

        $processor->process($opener, $closer, 1);

        $this->assertEquals('“', $opener->next()->getContent());
        $this->assertEquals('”', $closer->previous()->getContent());
    }
}
