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
use League\CommonMark\GithubFlavoredMarkdownConverter;
use PHPUnit\Framework\TestCase;

final class GithubFlavoredMarkdownConverterTest extends TestCase
{
    public function testEmptyConstructor(): void
    {
        $converter = new GithubFlavoredMarkdownConverter();

        $expectedEnvironment = Environment::createGFMEnvironment();

        $environment = $converter->getEnvironment();

        $this->assertCount(2, $environment->getExtensions());
        $this->assertInstanceOf(CommonMarkCoreExtension::class, $environment->getExtensions()[0]);
    }

    public function testConfigOnlyConstructor(): void
    {
        $config = ['foo' => 'bar'];

        $converter   = new GithubFlavoredMarkdownConverter($config);
        $environment = $converter->getEnvironment();

        $this->assertCount(2, $environment->getExtensions());
        $this->assertInstanceOf(CommonMarkCoreExtension::class, $environment->getExtensions()[0]);
        $this->assertSame('bar', $environment->getConfig('foo', 'DEFAULT'));
    }

    public function testConvertingInvalidUTF8(): void
    {
        $this->expectException(UnexpectedEncodingException::class);

        $converter = new GithubFlavoredMarkdownConverter();
        $converter->convertToHtml("\x09\xca\xca");
    }
}
