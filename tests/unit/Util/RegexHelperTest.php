<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Util;

use League\CommonMark\Util\RegexHelper;
use PHPUnit\Framework\TestCase;

/**
 * Tests the different regular expressions
 */
class RegexHelperTest extends TestCase
{
    public function testEscapable()
    {
        $regex = '/^' . RegexHelper::PARTIAL_ESCAPABLE . '$/';
        $this->assertRegExp($regex, '&');
        $this->assertRegExp($regex, '/');
        $this->assertRegExp($regex, '\\');
        $this->assertRegExp($regex, '(');
        $this->assertRegExp($regex, ')');
    }

    public function testEscapedChar()
    {
        $regex = '/^' . RegexHelper::PARTIAL_ESCAPED_CHAR . '$/';
        $this->assertRegExp($regex, '\\&');
        $this->assertRegExp($regex, '\\/');
        $this->assertRegExp($regex, '\\\\');
        $this->assertRegExp($regex, '\)');
        $this->assertRegExp($regex, '\(');
    }

    public function testInDoubleQuotes()
    {
        $regex = '/^' . RegexHelper::PARTIAL_IN_DOUBLE_QUOTES . '$/';
        $this->assertRegExp($regex, '"\\&"');
        $this->assertRegExp($regex, '"\\/"');
        $this->assertRegExp($regex, '"\\\\"');
    }

    public function testInSingleQuotes()
    {
        $regex = '/^' . RegexHelper::PARTIAL_IN_SINGLE_QUOTES . '$/';
        $this->assertRegExp($regex, '\'\\&\'');
        $this->assertRegExp($regex, '\'\\/\'');
        $this->assertRegExp($regex, '\'\\\\\'');
    }

    public function testInParens()
    {
        $regex = '/^' . RegexHelper::PARTIAL_IN_PARENS . '$/';
        $this->assertRegExp($regex, '(\\&)');
        $this->assertRegExp($regex, '(\\/)');
        $this->assertRegExp($regex, '(\\\\)');
    }

    public function testRegChar()
    {
        $regex = '/^' . RegexHelper::PARTIAL_REG_CHAR . '$/';
        $this->assertRegExp($regex, 'a');
        $this->assertRegExp($regex, 'A');
        $this->assertRegExp($regex, '!');
        $this->assertNotRegExp($regex, ' ');
    }

    public function testInParensNoSp()
    {
        $regex = '/^' . RegexHelper::PARTIAL_IN_PARENS_NOSP . '$/';
        $this->assertRegExp($regex, '(a)');
        $this->assertRegExp($regex, '(A)');
        $this->assertRegExp($regex, '(!)');
        $this->assertNotRegExp($regex, '(a )');
    }

    public function testTagname()
    {
        $regex = '/^' . RegexHelper::PARTIAL_TAGNAME . '$/';
        $this->assertRegExp($regex, 'a');
        $this->assertRegExp($regex, 'img');
        $this->assertRegExp($regex, 'h1');
        $this->assertNotRegExp($regex, '11');
    }

    public function testBlockTagName()
    {
        $regex = '/^' . RegexHelper::PARTIAL_BLOCKTAGNAME . '$/';
        $this->assertRegExp($regex, 'p');
        $this->assertRegExp($regex, 'div');
        $this->assertRegExp($regex, 'h1');
        $this->assertNotRegExp($regex, 'a');
        $this->assertNotRegExp($regex, 'h7');
    }

    public function testAttributeName()
    {
        $regex = '/^' . RegexHelper::PARTIAL_ATTRIBUTENAME . '$/';
        $this->assertRegExp($regex, 'href');
        $this->assertRegExp($regex, 'class');
        $this->assertRegExp($regex, 'data-src');
        $this->assertNotRegExp($regex, '-key');
    }

    public function testUnquotedValue()
    {
        $regex = '/^' . RegexHelper::PARTIAL_UNQUOTEDVALUE . '$/';
        $this->assertRegExp($regex, 'foo');
        $this->assertRegExp($regex, 'bar');
        $this->assertNotRegExp($regex, '"baz"');
    }

    public function testSingleQuotedValue()
    {
        $regex = '/^' . RegexHelper::PARTIAL_SINGLEQUOTEDVALUE . '$/';
        $this->assertRegExp($regex, '\'foo\'');
        $this->assertRegExp($regex, '\'bar\'');
        $this->assertNotRegExp($regex, '"baz"');
    }

    public function testDoubleQuotedValue()
    {
        $regex = '/^' . RegexHelper::PARTIAL_DOUBLEQUOTEDVALUE . '$/';
        $this->assertRegExp($regex, '"foo"');
        $this->assertRegExp($regex, '"bar"');
        $this->assertNotRegExp($regex, '\'baz\'');
    }

    public function testAttributeValue()
    {
        $regex = '/^' . RegexHelper::PARTIAL_ATTRIBUTEVALUE . '$/';
        $this->assertRegExp($regex, 'foo');
        $this->assertRegExp($regex, '\'bar\'');
        $this->assertRegExp($regex, '"baz"');
    }

    public function testAttributeValueSpec()
    {
        $regex = '/^' . RegexHelper::PARTIAL_ATTRIBUTEVALUESPEC . '$/';
        $this->assertRegExp($regex, '=foo');
        $this->assertRegExp($regex, '= foo');
        $this->assertRegExp($regex, ' =foo');
        $this->assertRegExp($regex, ' = foo');
        $this->assertRegExp($regex, '=\'bar\'');
        $this->assertRegExp($regex, '= \'bar\'');
        $this->assertRegExp($regex, ' =\'bar\'');
        $this->assertRegExp($regex, ' = \'bar\'');
        $this->assertRegExp($regex, '="baz"');
        $this->assertRegExp($regex, '= "baz"');
        $this->assertRegExp($regex, ' ="baz"');
        $this->assertRegExp($regex, ' = "baz"');
    }

    public function testAttribute()
    {
        $regex = '/^' . RegexHelper::PARTIAL_ATTRIBUTE . '$/';
        $this->assertRegExp($regex, ' disabled');
        $this->assertRegExp($regex, ' disabled="disabled"');
        $this->assertRegExp($regex, ' href="http://www.google.com"');
        $this->assertNotRegExp($regex, 'disabled', 'There must be at least one space at the start');
    }

    public function testOpenTag()
    {
        $regex = '/^' . RegexHelper::PARTIAL_OPENTAG . '$/';
        $this->assertRegExp($regex, '<hr>');
        $this->assertRegExp($regex, '<a href="http://www.google.com">');
        $this->assertRegExp($regex, '<img src="http://www.google.com/logo.png" />');
        $this->assertNotRegExp($regex, '</p>');
    }

    public function testCloseTag()
    {
        $regex = '/^' . RegexHelper::PARTIAL_CLOSETAG . '$/';
        $this->assertRegExp($regex, '</p>');
        $this->assertRegExp($regex, '</a>');
        $this->assertNotRegExp($regex, '<hr>');
        $this->assertNotRegExp($regex, '<img src="http://www.google.com/logo.png" />');
    }

    public function testOpenBlockTag()
    {
        $regex = '/^' . RegexHelper::PARTIAL_OPENBLOCKTAG . '$/';
        $this->assertRegExp($regex, '<body>');
        $this->assertRegExp($regex, '<hr>');
        $this->assertRegExp($regex, '<hr />');
        $this->assertRegExp($regex, '<p id="foo" class="bar">');
        $this->assertNotRegExp($regex, '<a href="http://www.google.com">', 'This is not a block element');
        $this->assertNotRegExp($regex, '</p>', 'This is not an opening tag');
    }

    public function testCloseBlockTag()
    {
        $regex = '/^' . RegexHelper::PARTIAL_CLOSEBLOCKTAG . '$/';
        $this->assertRegExp($regex, '</body>');
        $this->assertRegExp($regex, '</p>');
        $this->assertNotRegExp($regex, '</a>', 'This is not a block element');
        $this->assertNotRegExp($regex, '<br>', 'This is not a closing tag');
    }

    public function testHtmlComment()
    {
        $regex = '/^' . RegexHelper::PARTIAL_HTMLCOMMENT . '$/';
        $this->assertRegExp($regex, '<!---->');
        $this->assertRegExp($regex, '<!-- -->');
        $this->assertRegExp($regex, '<!-- HELLO WORLD -->');
        $this->assertNotRegExp($regex, '<!->');
        $this->assertNotRegExp($regex, '<!-->');
        $this->assertNotRegExp($regex, '<!--->');
        $this->assertNotRegExp($regex, '<!- ->');
    }

    public function testProcessingInstruction()
    {
        $regex = '/^' . RegexHelper::PARTIAL_PROCESSINGINSTRUCTION . '$/';
        $this->assertRegExp($regex, '<?PITarget PIContent?>');
        $this->assertRegExp($regex, '<?xml-stylesheet type="text/xsl" href="style.xsl"?>');
    }

    public function testDeclaration()
    {
        $regex = '/^' . RegexHelper::PARTIAL_DECLARATION . '$/';
        $this->assertRegExp($regex, '<!DOCTYPE html>');
        $this->assertRegExp($regex, '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">');
        $this->assertRegExp($regex, '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
    }

    public function testCDATA()
    {
        $regex = '/^' . RegexHelper::PARTIAL_CDATA . '$/';
        $this->assertRegExp($regex, '<![CDATA[<sender>John Smith</sender>]]>');
        $this->assertRegExp($regex, '<![CDATA[]]]]><![CDATA[>]]>');
    }

    public function testHtmlTag()
    {
        $regex = '/^' . RegexHelper::PARTIAL_HTMLTAG . '$/';
        $this->assertRegExp($regex, '<body id="main">');
        $this->assertRegExp($regex, '</p>');
        $this->assertRegExp($regex, '<!-- HELLO WORLD -->');
        $this->assertRegExp($regex, '<?xml-stylesheet type="text/xsl" href="style.xsl"?>');
        $this->assertRegExp($regex, '<!DOCTYPE html>');
        $this->assertRegExp($regex, '<![CDATA[<sender>John Smith</sender>]]>');
    }

    public function testHtmlBlockOpen()
    {
        $regex = '/^' . RegexHelper::PARTIAL_HTMLBLOCKOPEN . '$/';
        $this->assertRegExp($regex, '<h1>');
        $this->assertRegExp($regex, '</p>');
    }

    public function testLinkTitle()
    {
        $regex = '/^' . RegexHelper::PARTIAL_HTMLBLOCKOPEN . '$/';
        $this->assertRegExp($regex, '<h1>');
        $this->assertRegExp($regex, '</p>');
    }

    public function testUnescape()
    {
        $this->assertEquals('foo(and(bar))', RegexHelper::unescape('foo(and\\(bar\\))'));
    }

    public function testIsEscapable()
    {
        $this->assertFalse(RegexHelper::isEscapable(null));
        $this->assertFalse(RegexHelper::isEscapable('A'));
        $this->assertTrue(RegexHelper::isEscapable('\\'));
    }

    /**
     * @param $regex
     * @param $string
     * @param $offset
     * @param $expectedResult
     *
     * @dataProvider dataForTestMatchAt
     */
    public function testMatchAt($regex, $string, $offset, $expectedResult)
    {
        $this->assertEquals($expectedResult, RegexHelper::matchAt($regex, $string, $offset));
    }

    /**
     * @return array
     */
    public function dataForTestMatchAt()
    {
        return [
            ['/ /', 'foo bar', null, 3],
            ['/ /', 'foo bar', 0, 3],
            ['/ /', 'foo bar', 1, 3],
            ['/ /', 'это тест', null, 3],
            ['/ /', 'это тест', 0, 3],
            ['/ /', 'это тест', 1, 3],
        ];
    }

    /**
     * @param mixed  $constant
     * @param string $expectedValue
     *
     * @dataProvider dataForTestGetPartialRegex
     *
     * @deprecated
     */
    public function testGetPartialRegex($constant, $expectedValue)
    {
        $this->assertEquals($expectedValue, RegexHelper::getInstance()->getPartialRegex($constant));
    }

    /**
     * @return array
     */
    public function dataForTestGetPartialRegex()
    {
        $data = [];

        $c = new \ReflectionClass(RegexHelper::class);

        foreach ($c->getConstants() as $constant => $value) {
            if (is_numeric($value)) {
                $data[] = [$value, constant(RegexHelper::class . '::PARTIAL_' . $constant)];
            }
        }

        return $data;
    }

    public function testGetHtmlTagRegex()
    {
        $this->assertEquals('/^' . RegexHelper::PARTIAL_HTMLTAG . '/i', RegexHelper::getInstance()->getHtmlTagRegex());
    }

    public function testGetLinkTitleRegex()
    {
        $this->assertEquals('/' . RegexHelper::PARTIAL_LINK_TITLE . '/', RegexHelper::getInstance()->getLinkTitleRegex());
    }

    public function testGetLinkDestinationBracesRegex()
    {
        $this->assertEquals(RegexHelper::REGEX_LINK_DESTINATION_BRACES, RegexHelper::getInstance()->getLinkDestinationBracesRegex());
    }

    public function testGetThematicBreakRegex()
    {
        $this->assertEquals(RegexHelper::REGEX_THEMATIC_BREAK, RegexHelper::getInstance()->getThematicBreakRegex());
    }
}
