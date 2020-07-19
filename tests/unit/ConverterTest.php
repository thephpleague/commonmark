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

use League\CommonMark\Converter;
use PHPUnit\Framework\TestCase;

/**
 * @group legacy
 */
class ConverterTest extends TestCase
{
    public function testInvokeReturnsSameOutputAsConvertToHtml()
    {
        $inputMarkdown = '**Strong**';
        $expectedHtml = '<strong>Strong</strong>';

        /** @var Converter|\PHPUnit\Framework\MockObject\MockObject $converter */
        $converter = $this->getMockBuilder(Converter::class)
            ->disableOriginalConstructor()
            ->setMethods(['convertToHtml'])
            ->getMock();
        $converter->method('convertToHtml')->with($inputMarkdown)->willReturn($expectedHtml);

        $this->assertSame($expectedHtml, $converter($inputMarkdown));
    }
}
