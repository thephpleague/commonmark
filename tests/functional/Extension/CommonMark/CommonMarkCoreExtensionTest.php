<?php

declare(strict_types=1);

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
     *
     * @param array<string, mixed> $config
     */
    public function testConfiguration(string $markdown, array $config, string $expected): void
    {
        $converter = new CommonMarkConverter($config);

        $this->assertSame($expected, (string) $converter->convert($markdown));
    }

    /**
     * @return iterable<array<mixed>>
     */
    public static function getTestData(): iterable
    {
        yield ['*Emphasis*', [], "<p><em>Emphasis</em></p>\n"];
        yield ['**Strong**', [], "<p><strong>Strong</strong></p>\n"];
        yield ['_Emphasis_', [], "<p><em>Emphasis</em></p>\n"];
        yield ['__Strong__', [], "<p><strong>Strong</strong></p>\n"];

        yield ['*Emphasis*', ['commonmark' => ['enable_em' => true]], "<p><em>Emphasis</em></p>\n"];
        yield ['**Strong**', ['commonmark' => ['enable_strong' => true]], "<p><strong>Strong</strong></p>\n"];
        yield ['_Emphasis_', ['commonmark' => ['use_underscore' => true]], "<p><em>Emphasis</em></p>\n"];
        yield ['__Strong__', ['commonmark' => ['use_underscore' => true]], "<p><strong>Strong</strong></p>\n"];

        yield ['*Emphasis*', ['commonmark' => ['enable_em' => false]], "<p>*Emphasis*</p>\n"];
        yield ['**Strong**', ['commonmark' => ['enable_strong' => false]], "<p>**Strong**</p>\n"];
        yield ['_Emphasis_', ['commonmark' => ['use_underscore' => false]], "<p>_Emphasis_</p>\n"];
        yield ['__Strong__', ['commonmark' => ['use_underscore' => false]], "<p>__Strong__</p>\n"];
    }
}
