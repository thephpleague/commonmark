<?php

/*
 * This file is part of the league/commonmark-ext-strikethrough package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com> and uAfrica.com (http://uafrica.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Ext\Strikethrough\Test\StrikethroughTests;

use League\CommonMark\Ext\Strikethrough\StrikethroughExtension;
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;

class IntegrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $string
     * @param string $expected
     *
     * @dataProvider dataForIntegrationTest
     */
    public function testStrikethrough($string, $expected)
    {
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new StrikethroughExtension());

        $parser = new DocParser($environment);
        $renderer = new HtmlRenderer($environment);

        $document = $parser->parse($string);

        $html = $renderer->renderBlock($document);

        $this->assertSame($expected, $html);
    }

    /**
     * @return array
     */
    public function dataForIntegrationTest()
    {
        return [
            ['This is a test without any strikethroughs', "<p>This is a test without any strikethroughs</p>\n"],
            ['This is a test without any ~valid~ strikethroughs', "<p>This is a test without any ~valid~ strikethroughs</p>\n"],
            ['This is a test `without` any ~valid~ strikethroughs', "<p>This is a test <code>without</code> any ~valid~ strikethroughs</p>\n"],
            ['This is a ~~unit~~ integration test', "<p>This is a <del>unit</del> integration test</p>\n"],
            ['This ~~test has no ending match', "<p>This ~~test has no ending match</p>\n"],
            ['This ~~test~~~ has mismatched tildes', "<p>This ~~test~~~ has mismatched tildes</p>\n"],
            ['This ~~~test~~ also has mismatched tildes', "<p>This ~~~test~~ also has mismatched tildes</p>\n"],
            ['This one has ~~~three~~~ tildes', "<p>This one has <del>three</del> tildes</p>\n"],
        ];
    }
}
