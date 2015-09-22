<?php

/*
 * This is part of the webuni/commonmark-table-extension package.
 *
 * (c) Martin Hasoň <martin.hason@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webuni\CommonMark\TableExtension\tests\functional;

use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;
use Webuni\CommonMark\TableExtension\TableExtension;

class LocalDataTest extends \PHPUnit_Framework_TestCase
{
    private $parser;
    private $renderer;

    protected function setUp()
    {
        $environemnt = Environment::createCommonMarkEnvironment();
        $environemnt->addExtension(new TableExtension());

        $this->parser = new DocParser($environemnt);
        $this->renderer = new HtmlRenderer($environemnt);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testExample($markdown, $html, $testName)
    {
        $documentAST = $this->parser->parse($markdown);
        $actualResult = $this->renderer->renderBlock($documentAST);

        $failureMessage = sprintf('Unexpected result for "%s" test', $testName);
        $failureMessage .= "\n=== markdown ===============\n".$markdown;
        $failureMessage .= "\n=== expected ===============\n".$html;
        $failureMessage .= "\n=== got ====================\n".$actualResult;

        $this->assertEquals($html, $actualResult, $failureMessage);
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        $ret = [];
        foreach (glob(__DIR__.'/data/*.md') as $markdownFile) {
            $testName = basename($markdownFile, '.md');
            $markdown = file_get_contents($markdownFile);
            $html = file_get_contents(__DIR__.'/data/'.$testName.'.html');

            $ret[] = [$markdown, $html, $testName];
        }

        return $ret;
    }
}
