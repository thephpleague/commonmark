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

namespace League\CommonMark\Tests\Functional;

use League\CommonMark\CommonMarkConverter;
use PHPUnit\Framework\TestCase;

/**
 * Tests the parser against the CommonMark spec
 */
class SpecTest extends TestCase
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
     * @param string $section  Section of the spec
     * @param int    $number   Example number
     *
     * @dataProvider dataProvider
     */
    public function testExample($markdown, $html, $section, $number)
    {
        // Replace visible tabs in spec
        $markdown = str_replace('→', "\t", $markdown);
        $html = str_replace('→', "\t", $html);

        $actualResult = $this->converter->convertToHtml($markdown);

        $failureMessage = sprintf('Unexpected result ("%s" section, example #%d)', $section, $number);
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
        $filename = __DIR__ . '/../../vendor/commonmark/commonmark.js/test/spec.txt';
        if (($data = file_get_contents($filename)) === false) {
            $this->fail(sprintf('Failed to load spec from %s', $filename));
        }

        $matches = [];
        // Normalize newlines for platform independence
        $data = preg_replace('/\r\n?/', "\n", $data);
        $data = preg_replace('/<!-- END TESTS -->.*$/', '', $data);
        preg_match_all('/^`{32} example\n([\s\S]*?)^\.\n([\s\S]*?)^`{32}$|^#{1,6} *(.*)$/m', $data, $matches, PREG_SET_ORDER);

        $examples = [];
        $currentSection = '';
        $exampleNumber = 0;

        foreach ($matches as $match) {
            if (isset($match[3])) {
                $currentSection = $match[3];
            } else {
                $exampleNumber++;

                $markdown = $match[1];
                $markdown = str_replace('→', "\t", $markdown);

                $examples[] = [
                    'markdown' => $markdown,
                    'html'     => $match[2],
                    'section'  => $currentSection,
                    'number'   => $exampleNumber,
                ];
            }
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
