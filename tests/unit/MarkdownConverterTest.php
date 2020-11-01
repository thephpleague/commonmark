<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit;

use League\CommonMark\Environment;
use League\CommonMark\EnvironmentInterface;
use League\CommonMark\MarkdownConverter;
use PHPUnit\Framework\TestCase;

final class MarkdownConverterTest extends TestCase
{
    public function testConstructorAndGetEnvironment()
    {
        $environment = $this->createMock(EnvironmentInterface::class);

        $converter = new MarkdownConverter($environment);

        $this->assertSame($environment, $converter->getEnvironment());
    }

    public function testInvokeReturnsSameOutputAsConvertToHtml()
    {
        $inputMarkdown = '**Strong**';
        $expectedHtml = "<p><strong>Strong</strong></p>\n";

        $converter = new MarkdownConverter(Environment::createCommonMarkEnvironment());

        $this->assertSame($expectedHtml, $converter->convertToHtml($inputMarkdown));
        $this->assertSame($expectedHtml, $converter($inputMarkdown));
    }
}
