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
use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;
use PHPUnit\Framework\TestCase;

final class MarkdownConverterTest extends TestCase
{
    public function testConstructorAndGetEnvironment(): void
    {
        $environment = $this->createMock(EnvironmentInterface::class);

        $converter = new MarkdownConverter($environment);

        $this->assertSame($environment, $converter->getEnvironment());
    }

    public function testInvokeReturnsSameOutputAsConvert(): void
    {
        $inputMarkdown = '**Strong**';
        $expectedHtml  = "<p><strong>Strong</strong></p>\n";

        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $converter = new MarkdownConverter($environment);

        $this->assertSame($expectedHtml, (string) $converter->convert($inputMarkdown));
        $this->assertSame($expectedHtml, (string) $converter($inputMarkdown));
    }
}
