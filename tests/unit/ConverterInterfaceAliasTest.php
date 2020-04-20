<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\ConverterInterface;
use League\CommonMark\MarkdownConverterInterface;
use PHPUnit\Framework\TestCase;

class ConverterInterfaceAliasTest extends TestCase
{
    public function testAlias()
    {
        // Ensure the original interface is recognized as both itself and the new interface
        $converterImplementation = new class() implements ConverterInterface {
            public function convertToHtml(string $markdown): string
            {
                return '<p>test</p>';
            }
        };

        $this->assertInstanceOf(ConverterInterface::class, $converterImplementation);
        $this->assertInstanceOf(MarkdownConverterInterface::class, $converterImplementation);

        // Create a "legacy" function which requires the old interface and ensure we can still pass CommonMarkConverter (which implements the new interface) to it
        $legacyFunc = function (ConverterInterface $converter, string $markdown) {
            return $converter->convertToHtml($markdown);
        };

        // Create a "new" function which requires the new interface and ensure we can still pass
        // CommonMarkConverter to it
        $newFunc = function (MarkdownConverterInterface $converter, string $markdown) {
            return $converter->convertToHtml($markdown);
        };

        $converter = new CommonMarkConverter();
        $this->assertInstanceOf(MarkdownConverterInterface::class, $converter);
        $this->assertInstanceOf(ConverterInterface::class, $converter);

        $this->assertSame("<p>test</p>\n", $legacyFunc($converter, 'test'));
        $this->assertSame("<p>test</p>\n", $newFunc($converter, 'test'));
    }
}
