<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Functional\Extension\InlinesOnly;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\InlinesOnly\InlinesOnlyExtension;
use PHPUnit\Framework\TestCase;

/**
 * Tests the extension against sample files
 */
class InlinesOnlyFunctionalTest extends TestCase
{
    /**
     * @var CommonMarkConverter
     */
    protected $converter;

    protected function setUp(): void
    {
        $environment = new Environment();
        $environment->addExtension(new InlinesOnlyExtension());
        $this->converter = new CommonMarkConverter([], $environment);
    }

    /**
     * @param string $markdown Markdown to parse
     * @param string $html     Expected result
     *
     * @dataProvider dataProvider
     */
    public function testExample($markdown, $html)
    {
        $actualResult = $this->converter->convertToHtml($markdown);

        $failureMessage = 'Unexpected result';
        $failureMessage .= "\n=== markdown ===============\n" . $markdown;
        $failureMessage .= "\n=== expected ===============\n" . $html;
        $failureMessage .= "\n=== got ====================\n" . $actualResult;

        $this->assertEquals($html, $actualResult, $failureMessage);
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        $markdown = file_get_contents(__DIR__ . '/inlines.md');
        $html = file_get_contents(__DIR__ . '/inlines.html');

        return [
            [$markdown, $html],
        ];
    }
}
