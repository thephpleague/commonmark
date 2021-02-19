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
use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Exception\UnexpectedEncodingException;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Util\HtmlFilter;
use PHPUnit\Framework\TestCase;

class CommonMarkConverterTest extends TestCase
{
    public function testEmptyConstructor(): void
    {
        $converter = new CommonMarkConverter();

        $environment = $converter->getEnvironment();

        $this->assertCount(1, $environment->getExtensions());
        $this->assertInstanceOf(CommonMarkCoreExtension::class, $environment->getExtensions()[0]);
    }

    public function testConfigOnlyConstructor(): void
    {
        $config    = ['html_input' => HtmlFilter::ESCAPE];
        $converter = new CommonMarkConverter($config);

        $environment = $converter->getEnvironment();

        $this->assertCount(1, $environment->getExtensions());
        $this->assertInstanceOf(CommonMarkCoreExtension::class, $environment->getExtensions()[0]);
        $this->assertSame(HtmlFilter::ESCAPE, $environment->getConfiguration()->get('html_input'));
    }

    public function testConstructorWithConfigAndEnvironment(): void
    {
        $config            = ['html_input' => HtmlFilter::ESCAPE];
        $passedEnvironment = new Environment();

        $converter = new CommonMarkConverter($config, $passedEnvironment);

        $environment = $converter->getEnvironment();

        $this->assertSame($passedEnvironment, $environment);
        $this->assertSame(HtmlFilter::ESCAPE, $converter->getEnvironment()->getConfiguration()->get('html_input'));
    }

    public function testConstructorWithConfigAndGenericEnvironmentInterface(): void
    {
        $this->expectException(\RuntimeException::class);

        new CommonMarkConverter(['foo' => 'bar'], $this->createMock(EnvironmentInterface::class));
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
