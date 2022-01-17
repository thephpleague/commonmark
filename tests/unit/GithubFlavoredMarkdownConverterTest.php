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

use League\CommonMark\Environment\Environment;
use League\CommonMark\Exception\UnexpectedEncodingException;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use League\CommonMark\Util\HtmlFilter;
use PHPUnit\Framework\TestCase;

final class GithubFlavoredMarkdownConverterTest extends TestCase
{
    public function testEmptyConstructor(): void
    {
        $converter = new GithubFlavoredMarkdownConverter();

        $environment = $converter->getEnvironment();

        $this->assertCount(2, $environment->getExtensions());
        $this->assertInstanceOf(CommonMarkCoreExtension::class, $environment->getExtensions()[0]);
        $this->assertInstanceOf(GithubFlavoredMarkdownExtension::class, $environment->getExtensions()[1]);
    }

    public function testConfigOnlyConstructor(): void
    {
        $config = ['html_input' => HtmlFilter::ESCAPE];

        $converter   = new GithubFlavoredMarkdownConverter($config);
        $environment = $converter->getEnvironment();

        $this->assertSame(HtmlFilter::ESCAPE, $environment->getConfiguration()->get('html_input'));
    }

    public function testConvertingInvalidUTF8(): void
    {
        $this->expectException(UnexpectedEncodingException::class);

        $converter = new GithubFlavoredMarkdownConverter();
        $converter->convert("\x09\xca\xca");
    }

    public function testGetEnvironmentReturnsMainEnvironmentClass(): void
    {
        $converter = new GithubFlavoredMarkdownConverter();

        $this->assertInstanceOf(Environment::class, $converter->getEnvironment());
    }
}
