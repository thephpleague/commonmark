<?php

namespace League\CommonMark\Tests\Unit;

use League\CommonMark\Converter;
use PHPUnit_Framework_MockObject_MockObject;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    public function testInvokeReturnsSameOutputAsConvertToHtml()
    {
        $inputMarkdown = '**Strong**';
        $expectedHtml = '<strong>Strong</strong>';

        /** @var Converter|PHPUnit_Framework_MockObject_MockObject $converter */
        $converter = $this->getMockBuilder('League\CommonMark\Converter')
            ->disableOriginalConstructor()
            ->setMethods(['convertToHtml'])
            ->getMock();
        $converter->method('convertToHtml')->with($inputMarkdown)->willReturn($expectedHtml);

        $this->assertSame($expectedHtml, $converter($inputMarkdown));
    }
}
