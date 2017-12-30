<?php

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

namespace League\CommonMark\Tests\functional;

use League\CommonMark\CommonMarkConverter;
use PHPUnit\Framework\TestCase;

/**
 * Tests the parser against the CommonMark spec
 */
class RegressionTest extends TestCase
{
    /**
     * @var CommonMarkConverter
     */
    protected $converter;

    protected function setUp()
    {
        $this->converter = new CommonMarkConverter();
    }

    /**
     * @param string $markdown Markdown to parse
     * @param string $html     Expected result
     * @param int    $number   Example number
     *
     * @dataProvider dataProvider
     */
    public function testExample($markdown, $html, $number)
    {
        // Replace visible tabs in spec
        $markdown = str_replace('→', "\t", $markdown);
        $html = str_replace('→', "\t", $html);

        $actualResult = $this->converter->convertToHtml($markdown);

        $failureMessage = sprintf('Unexpected result (example #%d)', $number);
        $failureMessage .= "\n=== markdown ===============\n" . $this->showSpaces($markdown);
        $failureMessage .= "\n=== expected ===============\n" . $this->showSpaces($html);
        $failureMessage .= "\n=== got ====================\n" . $this->showSpaces($actualResult);

        $this->assertEquals($html, $actualResult, $failureMessage);
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        $filename = __DIR__ . '/../../vendor/commonmark/commonmark.js/test/regression.txt';
        if (($data = file_get_contents($filename)) === false) {
            $this->fail(sprintf('Failed to load regression tests from %s', $filename));
        }

        $matches = [];
        // Normalize newlines for platform independence
        $data = preg_replace('/\r\n?/', "\n", $data);
        $data = preg_replace('/<!-- END TESTS -->.*$/', '', $data);
        preg_match_all('/^`{32} example\n([\s\S]*?)^\.\n([\s\S]*?)^`{32}$|^#{1,6} *(.*)$/m', $data, $matches, PREG_SET_ORDER);

        $examples = [];
        $exampleNumber = 0;

        foreach ($matches as $match) {
            $exampleNumber++;

            $markdown = $match[1];
            $markdown = str_replace('→', "\t", $markdown);

            $examples[] = [
                'markdown' => $markdown,
                'html'     => $match[2],
                'number'   => $exampleNumber,
            ];
        }

        return $examples;
    }

    /**
     * @param string $str
     *
     * @return string
     */
    protected function showSpaces($str)
    {
        $str = str_replace("\t", '→', $str);
        $str = str_replace(' ', '␣', $str);

        return $str;
    }
}
