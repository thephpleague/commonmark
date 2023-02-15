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

namespace League\CommonMark\Tests\Functional\Extension\InlinesOnly;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\InlinesOnly\InlinesOnlyExtension;
use League\CommonMark\MarkdownConverter;
use PHPUnit\Framework\TestCase;

/**
 * Tests the extension against sample files
 */
final class InlinesOnlyFunctionalTest extends TestCase
{
    private MarkdownConverter $converter;

    protected function setUp(): void
    {
        $environment = new Environment();
        $environment->addExtension(new InlinesOnlyExtension());
        $this->converter = new MarkdownConverter($environment);
    }

    /**
     * @dataProvider dataProvider
     *
     * @param string $markdown Markdown to parse
     * @param string $html     Expected result
     */
    public function testExample(string $markdown, string $html): void
    {
        $actualResult = $this->converter->convert($markdown);

        $failureMessage  = 'Unexpected result';
        $failureMessage .= "\n=== markdown ===============\n" . $markdown;
        $failureMessage .= "\n=== expected ===============\n" . $html;
        $failureMessage .= "\n=== got ====================\n" . $actualResult;

        $this->assertEquals($html, $actualResult, $failureMessage);
    }

    /**
     * @return array<array<string>>
     */
    public static function dataProvider(): array
    {
        $markdown = \file_get_contents(__DIR__ . '/inlines.md');
        $html     = \file_get_contents(__DIR__ . '/inlines.html');

        return [
            [$markdown, $html],
        ];
    }
}
