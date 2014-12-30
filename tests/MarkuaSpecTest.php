<?php
namespace League\CommonMark\Tests;

use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\Environment\Markua;
use League\CommonMark\HtmlRenderer;

/**
 * Tests the parser against the Markua spec
 */
class MarkuaSpecTest extends \PHPUnit_Framework_TestCase {
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
        $environment = Environment::createEnvironment(new Markua());
        $this->docParser = new DocParser($environment);
        $this->htmlRenderer = new HtmlRenderer($environment);
    }

    /**
     * @param string $markdown Markdown to parse
     * @param string $html     Expected result
     *
     * @dataProvider dataProvider
     */
    public function testExample($title, $markdown, $html)
    {
        $this->setName($title);
        $docBlock = $this->docParser->parse($markdown);
        $actualResult = $this->htmlRenderer->renderBlock($docBlock);

        $this->assertEquals($html, $actualResult);
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        foreach (glob(__DIR__ . '/spec/markua/*.txt') as $spec) {
            $test = array();
            foreach (file($spec) as $line) {
                if (preg_match('/=== (.*?) ===/', trim($line), $matches)) {
                    $section = $matches[1];
                    if (strtolower($section) == 'end') {
                        $tests[] = $test;
                        $test = array();
                        continue;
                    }
                    $test[$section] = '';
                } elseif (strtolower($section) != 'end') {
                    $test[$section] .= $line;
                }
            }
        }

        return $tests;
    }
}
