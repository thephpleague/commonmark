<?php

/*
 * This file is part of the commonmark-php package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on stmd.js
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ColinODell\CommonMark\Tests;

use ColinODell\CommonMark\DocParser;
use ColinODell\CommonMark\HtmlRenderer;

/**
 * Tests the parser against the CommonMark spec
 */
class SpecTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DocParser
     */
    protected $docParser;

    /**
     * @var HtmlRenderer
     */
    protected $htmlRenderer;

    protected function setUp()
    {
        $this->docParser = new DocParser();
        $this->htmlRenderer = new HtmlRenderer();
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
        $docBlock = $this->docParser->parse($markdown);
        $actualResult = $this->htmlRenderer->render($docBlock);

        $failureMessage = sprintf('Unexpected result ("%s" section, example #%d)', $section, $number);

        $this->assertEquals($html, $actualResult, $failureMessage);
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        $filename = __DIR__ . '/../vendor/jgm/stmd/spec.txt';
        if (($data = file_get_contents($filename)) === false) {
            $this->fail(sprintf('Failed to load spec from %s', $filename));
        }

        $matches = array();
        $data = preg_replace('/^<!-- END TESTS -->(.|[\n])*/m', '', $data);
        preg_match_all('/^\.\n([\s\S]*?)^\.\n([\s\S]*?)^\.$|^#{1,6} *(.*)$/m', $data, $matches, PREG_SET_ORDER);

        $examples = array();
        $currentSection = "";
        $exampleNumber = 0;

        foreach ($matches as $match) {
            if (isset($match[3])) {
                $currentSection = $match[3];
            } else {
                $exampleNumber++;

                $markdown = $match[1];
                $markdown = str_replace('→', "\t", $markdown);

                $examples[] = array(
                    'markdown' => $markdown,
                    'html' => $match[2],
                    'section' => $currentSection,
                    'number' => $exampleNumber
                );
            }
        }

        return $examples;
    }
}
