<?php

/*
 * This is part of the webuni/commonmark-table-extension package.
 *
 * (c) Martin Hasoň <martin.hason@gmail.com>
 * (c) Webuni s.r.o. <info@webuni.cz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webuni\CommonMark\TableExtension\Tests\Functional;

use League\CommonMark\DocParser;
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;
use Webuni\CommonMark\TableExtension\TableExtension;

class LocalDataTest extends \PHPUnit_Framework_TestCase
{
    /* @var Environment */
    private $environment;
    private $parser;

    protected function setUp()
    {
        $this->environment = Environment::createCommonMarkEnvironment();
        $this->environment->addExtension(new TableExtension());

        $this->parser = new DocParser($this->environment);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testRenderer($markdown, $html, $testName)
    {
        $renderer = new HtmlRenderer($this->environment);
        $this->assertCommonMark($renderer, $markdown, $html, $testName);
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

    protected function assertCommonMark(ElementRendererInterface $renderer, $markdown, $html, $testName)
    {
        $documentAST = $this->parser->parse($markdown);
        $actualResult = $renderer->renderBlock($documentAST);

        $failureMessage = sprintf('Unexpected result for "%s" test', $testName);
        $failureMessage .= "\n=== markdown ===============\n".$markdown;
        $failureMessage .= "\n=== expected ===============\n".$html;
        $failureMessage .= "\n=== got ====================\n".$actualResult;

        $this->assertEquals($html, $actualResult, $failureMessage);
    }
}
