<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Functional\Extension\CommonMark;

use League\CommonMark\CommonMarkConverter;
use PHPUnit\Framework\TestCase;

final class CommonMarkCoreExtensionTest extends TestCase
{
    /**
     * @dataProvider getTestData
     */
    public function testConfiguration(string $markdown, array $config, string $expected): void
    {
        $converter = new CommonMarkConverter($config);

        $this->assertSame($expected, $converter->convertToHtml($markdown));
    }

    public function getTestData(): iterable
    {
        yield ['*Emphasis*', [], "<p><em>Emphasis</em></p>\n"];
        yield ['**Strong**', [], "<p><strong>Strong</strong></p>\n"];

        yield ['*Emphasis*', ['enable_em' => true], "<p><em>Emphasis</em></p>\n"];
        yield ['**Strong**', ['enable_strong' => true], "<p><strong>Strong</strong></p>\n"];
        yield ['*Emphasis*', ['commonmark' => ['enable_em' => true]], "<p><em>Emphasis</em></p>\n"];
        yield ['**Strong**', ['commonmark' => ['enable_strong' => true]], "<p><strong>Strong</strong></p>\n"];

        yield ['*Emphasis*', ['enable_em' => false], "<p>*Emphasis*</p>\n"];
        yield ['**Strong**', ['enable_strong' => false], "<p>**Strong**</p>\n"];
        yield ['*Emphasis*', ['commonmark' => ['enable_em' => false]], "<p>*Emphasis*</p>\n"];
        yield ['**Strong**', ['commonmark' => ['enable_strong' => false]], "<p>**Strong**</p>\n"];

        // New "commonmark" path takes priority over the legacy one
        yield ['*Emphasis*', ['commonmark' => ['enable_em' => false], 'enable_em' => true], "<p>*Emphasis*</p>\n"];
        yield ['**Strong**', ['commonmark' => ['enable_strong' => false], 'enable_em' => true], "<p>**Strong**</p>\n"];
        yield ['*Emphasis*', ['commonmark' => ['enable_em' => true], 'enable_em' => false], "<p><em>Emphasis</em></p>\n"];
        yield ['**Strong**', ['commonmark' => ['enable_strong' => true], 'enable_em' => false], "<p><strong>Strong</strong></p>\n"];

        ////////////////////////////

        yield ['**Strong**', ['use_asterisk' => true], "<p><strong>Strong</strong></p>\n"];
        yield ['**Strong**', ['use_asterisk' => false], "<p>**Strong**</p>\n"];
        yield ['**Strong**', ['commonmark' => ['use_asterisk' => true]], "<p><strong>Strong</strong></p>\n"];
        yield ['**Strong**', ['commonmark' => ['use_asterisk' => false]], "<p>**Strong**</p>\n"];
        yield ['**Strong**', ['commonmark' => ['use_asterisk' => true], 'use_asterisk' => false], "<p><strong>Strong</strong></p>\n"];
        yield ['**Strong**', ['commonmark' => ['use_asterisk' => false], 'use_asterisk' => true], "<p>**Strong**</p>\n"];

        /////////////////////////////

        yield ['__Strong__', ['use_underscore' => true], "<p><strong>Strong</strong></p>\n"];
        yield ['__Strong__', ['use_underscore' => false], "<p>__Strong__</p>\n"];
        yield ['__Strong__', ['commonmark' => ['use_underscore' => true]], "<p><strong>Strong</strong></p>\n"];
        yield ['__Strong__', ['commonmark' => ['use_underscore' => false]], "<p>__Strong__</p>\n"];
        yield ['__Strong__', ['commonmark' => ['use_underscore' => true], 'use_underscore' => false], "<p><strong>Strong</strong></p>\n"];
        yield ['__Strong__', ['commonmark' => ['use_underscore' => false], 'use_underscore' => true], "<p>__Strong__</p>\n"];
    }
}
