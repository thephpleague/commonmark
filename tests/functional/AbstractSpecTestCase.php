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

namespace League\CommonMark\Tests\Functional;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\MarkdownConverter;
use PHPUnit\Framework\TestCase;

abstract class AbstractSpecTestCase extends TestCase
{
    protected MarkdownConverter $converter;

    protected function setUp(): void
    {
        $this->converter = new CommonMarkConverter();
    }

    /**
     * @dataProvider dataProvider
     *
     * @param string $input  Markdown to parse
     * @param string $output Expected result
     */
    public function testSpecExample(string $input, string $output, string $type = '', string $section = '', int $number = -1): void
    {
        $actualResult = (string) $this->converter->convert($input);

        $failureMessage  = 'Unexpected result:';
        $failureMessage .= "\n=== markdown ===============\n" . $this->showSpaces($input);
        $failureMessage .= "\n=== expected ===============\n" . $this->showSpaces($output);
        $failureMessage .= "\n=== got ====================\n" . $this->showSpaces($actualResult);

        $this->assertEquals($output, $actualResult, $failureMessage);
    }

    abstract public static function dataProvider(): \Generator;

    private function showSpaces(string $str): string
    {
        return \strtr($str, ["\t" => '→', ' ' => '␣']);
    }
}
