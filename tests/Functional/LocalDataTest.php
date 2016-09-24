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
use Webuni\CommonMark\TwigRenderer\CommonMarkTwigExtension;
use Webuni\CommonMark\TwigRenderer\TwigRenderer;

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
    public function testHtmlRenderer($markdown, $html, $testName)
    {
        $renderer = new HtmlRenderer($this->environment);
        $this->assertCommonMark($renderer, $markdown, $html, $testName);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testTwigRenderer($markdown, $html, $testName)
    {
        $loader = CommonMarkTwigExtension::createTwigLoader();

        $ref = new \ReflectionClass('Webuni\CommonMark\TableExtension\TableExtension');
        $loader->addPath(dirname($ref->getFileName()).'/Resources');
        $loader->addPath(__DIR__);

        $twig = new \Twig_Environment($loader, [
            'strict_variables' => true,
        ]);
        $twig->addExtension(new CommonMarkTwigExtension());

        $this->environment->mergeConfig(['renderer' => ['twig_template' => 'template.html.twig']]);
        $renderer = new TwigRenderer($this->environment, $twig);

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

    private function assertCommonMark(ElementRendererInterface $renderer, $markdown, $html, $testName)
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
