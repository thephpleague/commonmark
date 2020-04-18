<?php

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
class LocalDataTest extends AbstractLocalDataTest
{
    /**
     * @var CommonMarkConverter
     */
    protected $converter;

    protected function setUp(): void
    {
        $this->converter = new CommonMarkConverter();
    }

    /**
     * @param string $markdown Markdown to parse
     * @param string $html     Expected result
     * @param string $testName Name of the test
     *
     * @dataProvider dataProvider
     */
    public function testExample($markdown, $html, $testName)
    {
        $this->assertMarkdownRendersAs($markdown, $html, $testName);
    }

    /**
     * @return iterable
     */
    public function dataProvider()
    {
        yield from $this->loadTests(__DIR__ . '/data');
    }
}
