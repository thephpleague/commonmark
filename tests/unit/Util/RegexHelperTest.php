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

use League\CommonMark\Block\Element\HtmlBlock;
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
        $this->assertRegexMatches($regex, '&');
        $this->assertRegexMatches($regex, '/');
        $this->assertRegexMatches($regex, '\\');
        $this->assertRegexMatches($regex, '(');
        $this->assertRegexMatches($regex, ')');
    }

    public function testEscapedChar()
    {
        $regex = '/^' . RegexHelper::PARTIAL_ESCAPED_CHAR . '$/';
        $this->assertRegexMatches($regex, '\\&');
        $this->assertRegexMatches($regex, '\\/');
        $this->assertRegexMatches($regex, '\\\\');
        $this->assertRegexMatches($regex, '\)');
        $this->assertRegexMatches($regex, '\(');
    }

    public function testInDoubleQuotes()
    {
        $regex = '/^' . RegexHelper::PARTIAL_IN_DOUBLE_QUOTES . '$/';
        $this->assertRegexMatches($regex, '"\\&"');
        $this->assertRegexMatches($regex, '"\\/"');
        $this->assertRegexMatches($regex, '"\\\\"');
    }

    public function testInSingleQuotes()
    {
        $regex = '/^' . RegexHelper::PARTIAL_IN_SINGLE_QUOTES . '$/';
        $this->assertRegexMatches($regex, '\'\\&\'');
        $this->assertRegexMatches($regex, '\'\\/\'');
        $this->assertRegexMatches($regex, '\'\\\\\'');
    }

    public function testInParens()
    {
        $regex = '/^' . RegexHelper::PARTIAL_IN_PARENS . '$/';
        $this->assertRegexMatches($regex, '(\\&)');
        $this->assertRegexMatches($regex, '(\\/)');
        $this->assertRegexMatches($regex, '(\\\\)');
    }

    public function testRegChar()
    {
        $regex = '/^' . RegexHelper::PARTIAL_REG_CHAR . '$/';
        $this->assertRegexMatches($regex, 'a');
        $this->assertRegexMatches($regex, 'A');
        $this->assertRegexMatches($regex, '!');
        $this->assertRegexDoesNotMatch($regex, ' ');
    }

    public function testInParensNoSp()
    {
        $regex = '/^' . RegexHelper::PARTIAL_IN_PARENS_NOSP . '$/';
        $this->assertRegexMatches($regex, '(a)');
        $this->assertRegexMatches($regex, '(A)');
        $this->assertRegexMatches($regex, '(!)');
        $this->assertRegexDoesNotMatch($regex, '(a )');
    }

    public function testTagname()
    {
        $regex = '/^' . RegexHelper::PARTIAL_TAGNAME . '$/';
        $this->assertRegexMatches($regex, 'a');
        $this->assertRegexMatches($regex, 'img');
        $this->assertRegexMatches($regex, 'h1');
        $this->assertRegexDoesNotMatch($regex, '11');
    }

    public function testBlockTagName()
    {
        $regex = '/^' . RegexHelper::PARTIAL_BLOCKTAGNAME . '$/';
        $this->assertRegexMatches($regex, 'p');
        $this->assertRegexMatches($regex, 'div');
        $this->assertRegexMatches($regex, 'h1');
        $this->assertRegexDoesNotMatch($regex, 'a');
        $this->assertRegexDoesNotMatch($regex, 'h7');
    }

    public function testAttributeName()
    {
        $regex = '/^' . RegexHelper::PARTIAL_ATTRIBUTENAME . '$/';
        $this->assertRegexMatches($regex, 'href');
        $this->assertRegexMatches($regex, 'class');
        $this->assertRegexMatches($regex, 'data-src');
        $this->assertRegexDoesNotMatch($regex, '-key');
    }

    public function testUnquotedValue()
    {
        $regex = '/^' . RegexHelper::PARTIAL_UNQUOTEDVALUE . '$/';
        $this->assertRegexMatches($regex, 'foo');
        $this->assertRegexMatches($regex, 'bar');
        $this->assertRegexDoesNotMatch($regex, '"baz"');
    }

    public function testSingleQuotedValue()
    {
        $regex = '/^' . RegexHelper::PARTIAL_SINGLEQUOTEDVALUE . '$/';
        $this->assertRegexMatches($regex, '\'foo\'');
        $this->assertRegexMatches($regex, '\'bar\'');
        $this->assertRegexDoesNotMatch($regex, '"baz"');
    }

    public function testDoubleQuotedValue()
    {
        $regex = '/^' . RegexHelper::PARTIAL_DOUBLEQUOTEDVALUE . '$/';
        $this->assertRegexMatches($regex, '"foo"');
        $this->assertRegexMatches($regex, '"bar"');
        $this->assertRegexDoesNotMatch($regex, '\'baz\'');
    }

    public function testAttributeValue()
    {
        $regex = '/^' . RegexHelper::PARTIAL_ATTRIBUTEVALUE . '$/';
        $this->assertRegexMatches($regex, 'foo');
        $this->assertRegexMatches($regex, '\'bar\'');
        $this->assertRegexMatches($regex, '"baz"');
    }

    public function testAttributeValueSpec()
    {
        $regex = '/^' . RegexHelper::PARTIAL_ATTRIBUTEVALUESPEC . '$/';
        $this->assertRegexMatches($regex, '=foo');
        $this->assertRegexMatches($regex, '= foo');
        $this->assertRegexMatches($regex, ' =foo');
        $this->assertRegexMatches($regex, ' = foo');
        $this->assertRegexMatches($regex, '=\'bar\'');
        $this->assertRegexMatches($regex, '= \'bar\'');
        $this->assertRegexMatches($regex, ' =\'bar\'');
        $this->assertRegexMatches($regex, ' = \'bar\'');
        $this->assertRegexMatches($regex, '="baz"');
        $this->assertRegexMatches($regex, '= "baz"');
        $this->assertRegexMatches($regex, ' ="baz"');
        $this->assertRegexMatches($regex, ' = "baz"');
    }

    public function testAttribute()
    {
        $regex = '/^' . RegexHelper::PARTIAL_ATTRIBUTE . '$/';
        $this->assertRegexMatches($regex, ' disabled');
        $this->assertRegexMatches($regex, ' disabled="disabled"');
        $this->assertRegexMatches($regex, ' href="http://www.google.com"');
        $this->assertRegexDoesNotMatch($regex, 'disabled', 'There must be at least one space at the start');
    }

    public function testOpenTag()
    {
        $regex = '/^' . RegexHelper::PARTIAL_OPENTAG . '$/';
        $this->assertRegexMatches($regex, '<hr>');
        $this->assertRegexMatches($regex, '<a href="http://www.google.com">');
        $this->assertRegexMatches($regex, '<img src="http://www.google.com/logo.png" />');
        $this->assertRegexDoesNotMatch($regex, '</p>');
    }

    public function testCloseTag()
    {
        $regex = '/^' . RegexHelper::PARTIAL_CLOSETAG . '$/';
        $this->assertRegexMatches($regex, '</p>');
        $this->assertRegexMatches($regex, '</a>');
        $this->assertRegexDoesNotMatch($regex, '<hr>');
        $this->assertRegexDoesNotMatch($regex, '<img src="http://www.google.com/logo.png" />');
    }

    public function testOpenBlockTag()
    {
        $regex = '/^' . RegexHelper::PARTIAL_OPENBLOCKTAG . '$/';
        $this->assertRegexMatches($regex, '<body>');
        $this->assertRegexMatches($regex, '<hr>');
        $this->assertRegexMatches($regex, '<hr />');
        $this->assertRegexMatches($regex, '<p id="foo" class="bar">');
        $this->assertRegexDoesNotMatch($regex, '<a href="http://www.google.com">', 'This is not a block element');
        $this->assertRegexDoesNotMatch($regex, '</p>', 'This is not an opening tag');
    }

    public function testCloseBlockTag()
    {
        $regex = '/^' . RegexHelper::PARTIAL_CLOSEBLOCKTAG . '$/';
        $this->assertRegexMatches($regex, '</body>');
        $this->assertRegexMatches($regex, '</p>');
        $this->assertRegexDoesNotMatch($regex, '</a>', 'This is not a block element');
        $this->assertRegexDoesNotMatch($regex, '<br>', 'This is not a closing tag');
    }

    public function testHtmlComment()
    {
        $regex = '/^' . RegexHelper::PARTIAL_HTMLCOMMENT . '$/';
        $this->assertRegexMatches($regex, '<!---->');
        $this->assertRegexMatches($regex, '<!-- -->');
        $this->assertRegexMatches($regex, '<!-- HELLO WORLD -->');
        $this->assertRegexDoesNotMatch($regex, '<!->');
        $this->assertRegexDoesNotMatch($regex, '<!-->');
        $this->assertRegexDoesNotMatch($regex, '<!--->');
        $this->assertRegexDoesNotMatch($regex, '<!- ->');
    }

    public function testProcessingInstruction()
    {
        $regex = '/^' . RegexHelper::PARTIAL_PROCESSINGINSTRUCTION . '$/';
        $this->assertRegexMatches($regex, '<?PITarget PIContent?>');
        $this->assertRegexMatches($regex, '<?xml-stylesheet type="text/xsl" href="style.xsl"?>');
    }

    public function testDeclaration()
    {
        $regex = '/^' . RegexHelper::PARTIAL_DECLARATION . '$/';
        $this->assertRegexMatches($regex, '<!DOCTYPE html>');
        $this->assertRegexMatches($regex, '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">');
        $this->assertRegexMatches($regex, '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
    }

    public function testCDATA()
    {
        $regex = '/^' . RegexHelper::PARTIAL_CDATA . '$/';
        $this->assertRegexMatches($regex, '<![CDATA[<sender>John Smith</sender>]]>');
        $this->assertRegexMatches($regex, '<![CDATA[]]]]><![CDATA[>]]>');
    }

    public function testHtmlTag()
    {
        $regex = '/^' . RegexHelper::PARTIAL_HTMLTAG . '$/';
        $this->assertRegexMatches($regex, '<body id="main">');
        $this->assertRegexMatches($regex, '</p>');
        $this->assertRegexMatches($regex, '<!-- HELLO WORLD -->');
        $this->assertRegexMatches($regex, '<?xml-stylesheet type="text/xsl" href="style.xsl"?>');
        $this->assertRegexMatches($regex, '<!DOCTYPE html>');
        $this->assertRegexMatches($regex, '<![CDATA[<sender>John Smith</sender>]]>');
    }

    public function testHtmlBlockOpen()
    {
        $regex = '/^' . RegexHelper::PARTIAL_HTMLBLOCKOPEN . '$/';
        $this->assertRegexMatches($regex, '<h1>');
        $this->assertRegexMatches($regex, '</p>');
    }

    public function testLinkTitle()
    {
        $regex = '/^' . RegexHelper::PARTIAL_HTMLBLOCKOPEN . '$/';
        $this->assertRegexMatches($regex, '<h1>');
        $this->assertRegexMatches($regex, '</p>');
    }

    public function testUnescape()
    {
        $this->assertEquals('foo(and(bar))', RegexHelper::unescape('foo(and\\(bar\\))'));
    }

    public function testIsEscapable()
    {
        $this->assertFalse(RegexHelper::isEscapable(''));
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
    public function testMatchAt(string $regex, string $string, ?int $offset, int $expectedResult)
    {
        if ($offset === null) {
            $this->assertEquals($expectedResult, RegexHelper::matchAt($regex, $string));
        } else {
            $this->assertEquals($expectedResult, RegexHelper::matchAt($regex, $string, $offset));
        }
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
     * @param int $type
     *
     * @dataProvider blockTypesWithValidOpenerRegexes
     */
    public function testValidHtmlBlockOpenRegex(int $type)
    {
        $this->assertNotEmpty(RegexHelper::getHtmlBlockOpenRegex($type));
    }

    public function blockTypesWithValidOpenerRegexes()
    {
        yield [HtmlBlock::TYPE_1_CODE_CONTAINER];
        yield [HtmlBlock::TYPE_2_COMMENT];
        yield [HtmlBlock::TYPE_3];
        yield [HtmlBlock::TYPE_4];
        yield [HtmlBlock::TYPE_5_CDATA];
        yield [HtmlBlock::TYPE_6_BLOCK_ELEMENT];
        yield [HtmlBlock::TYPE_7_MISC_ELEMENT];
    }

    public function testInvalidHtmlBlockOpenRegex()
    {
        $this->expectException(\InvalidArgumentException::class);

        RegexHelper::getHtmlBlockOpenRegex(8);
    }

    /**
     * @param int $type
     *
     * @dataProvider blockTypesWithValidCloserRegexes
     */
    public function testValidHtmlBlockCloseRegex(int $type)
    {
        $this->assertNotEmpty(RegexHelper::getHtmlBlockOpenRegex($type));
    }

    public function blockTypesWithValidCloserRegexes()
    {
        yield [HtmlBlock::TYPE_1_CODE_CONTAINER];
        yield [HtmlBlock::TYPE_2_COMMENT];
        yield [HtmlBlock::TYPE_3];
        yield [HtmlBlock::TYPE_4];
        yield [HtmlBlock::TYPE_5_CDATA];
    }

    /**
     * @param int $type
     *
     * @dataProvider blockTypesWithInvalidCloserRegexes
     */
    public function testInvalidHtmlBlockCloseRegex(int $type)
    {
        $this->expectException(\InvalidArgumentException::class);

        RegexHelper::getHtmlBlockCloseRegex($type);
    }

    public function blockTypesWithInvalidCloserRegexes()
    {
        yield [HtmlBlock::TYPE_6_BLOCK_ELEMENT];
        yield [HtmlBlock::TYPE_7_MISC_ELEMENT];
        yield [8];
    }

    private function assertRegexMatches(string $pattern, string $string, string $message = ''): void
    {
        if (\method_exists($this, 'assertMatchesRegularExpression')) {
            $this->assertMatchesRegularExpression($pattern, $string, $message);
        } else {
            $this->assertRegExp($pattern, $string, $message);
        }
    }

    private function assertRegexDoesNotMatch(string $pattern, string $string, string $message = ''): void
    {
        if (\method_exists($this, 'assertDoesNotMatchRegularExpression')) {
            $this->assertDoesNotMatchRegularExpression($pattern, $string, $message);
        } else {
            $this->assertNotRegExp($pattern, $string, $message);
        }
    }
}
