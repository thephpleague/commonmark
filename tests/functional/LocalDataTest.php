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

namespace League\CommonMark\Tests\Functional;

use League\CommonMark\CommonMarkConverter;

/**
 * Tests the parser against locally-stored examples
 *
 * This is particularly useful for testing minor variations allowed by the spec
 * or small regressions not tested by the spec.
 */
final class LocalDataTest extends AbstractLocalDataTest
{
    protected function setUp(): void
    {
        $this->converter = new CommonMarkConverter();
    }

    /**
     * @dataProvider dataProvider
     *
     * @param string $markdown Markdown to parse
     * @param string $html     Expected result
     * @param string $testName Name of the test
     */
    public function testExample(string $markdown, string $html, string $testName): void
    {
        $this->assertMarkdownRendersAs($markdown, $html, $testName);
    }

    /**
     * @return iterable<array<string>>
     */
    public function dataProvider(): iterable
    {
        yield from $this->loadTests(__DIR__ . '/data');
    }
}
