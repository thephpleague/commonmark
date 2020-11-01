<?php

declare(strict_types=1);

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

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Exception\UnexpectedEncodingException;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use PHPUnit\Framework\TestCase;

class CommonMarkConverterTest extends TestCase
{
    public function testEmptyConstructor(): void
    {
        $converter           = new CommonMarkConverter();
        $expectedEnvironment = Environment::createCommonMarkEnvironment();

        $environment = $converter->getEnvironment();

        $this->assertCount(1, $environment->getExtensions());
        $this->assertInstanceOf(CommonMarkCoreExtension::class, $environment->getExtensions()[0]);
    }

    public function testConfigPassedIntoConstructor(): void
    {
        $config    = ['foo' => 'bar'];
        $converter = new CommonMarkConverter($config);

        $environment = $converter->getEnvironment();

        $this->assertCount(1, $environment->getExtensions());
        $this->assertInstanceOf(CommonMarkCoreExtension::class, $environment->getExtensions()[0]);
        $this->assertSame('bar', $environment->getConfig('foo', 'DEFAULT'));
    }

    public function testConvertingInvalidUTF8(): void
    {
        $this->expectException(UnexpectedEncodingException::class);

        $converter = new CommonMarkConverter();
        $converter->convertToHtml("\x09\xca\xca");
    }

    public function testInvokeReturnsSameOutputAsConvertToHtml(): void
    {
        $inputMarkdown = '**Strong**';

        $converter = new CommonMarkConverter();

        $this->assertEquals($converter->convertToHtml($inputMarkdown), $converter($inputMarkdown));
    }
}
