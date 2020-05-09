<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Util;

use League\CommonMark\Extension\CommonMark\Node\Block\HtmlBlock;
use League\CommonMark\Util\RegexHelper;
use PHPUnit\Framework\TestCase;

/**
 * Tests the different regular expressions
 */
class RegexHelperTest extends TestCase
{
    public function testEscapable(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_ESCAPABLE . '$/';
        $this->assertRegExp($regex, '&');
        $this->assertRegExp($regex, '/');
        $this->assertRegExp($regex, '\\');
        $this->assertRegExp($regex, '(');
        $this->assertRegExp($regex, ')');
    }

    public function testEscapedChar(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_ESCAPED_CHAR . '$/';
        $this->assertRegExp($regex, '\\&');
        $this->assertRegExp($regex, '\\/');
        $this->assertRegExp($regex, '\\\\');
        $this->assertRegExp($regex, '\)');
        $this->assertRegExp($regex, '\(');
    }

    public function testInDoubleQuotes(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_IN_DOUBLE_QUOTES . '$/';
        $this->assertRegExp($regex, '"\\&"');
        $this->assertRegExp($regex, '"\\/"');
        $this->assertRegExp($regex, '"\\\\"');
    }

    public function testInSingleQuotes(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_IN_SINGLE_QUOTES . '$/';
        $this->assertRegExp($regex, '\'\\&\'');
        $this->assertRegExp($regex, '\'\\/\'');
        $this->assertRegExp($regex, '\'\\\\\'');
    }

    public function testInParens(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_IN_PARENS . '$/';
        $this->assertRegExp($regex, '(\\&)');
        $this->assertRegExp($regex, '(\\/)');
        $this->assertRegExp($regex, '(\\\\)');
    }

    public function testRegChar(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_REG_CHAR . '$/';
        $this->assertRegExp($regex, 'a');
        $this->assertRegExp($regex, 'A');
        $this->assertRegExp($regex, '!');
        $this->assertNotRegExp($regex, ' ');
    }

    public function testInParensNoSp(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_IN_PARENS_NOSP . '$/';
        $this->assertRegExp($regex, '(a)');
        $this->assertRegExp($regex, '(A)');
        $this->assertRegExp($regex, '(!)');
        $this->assertNotRegExp($regex, '(a )');
    }

    public function testTagname(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_TAGNAME . '$/';
        $this->assertRegExp($regex, 'a');
        $this->assertRegExp($regex, 'img');
        $this->assertRegExp($regex, 'h1');
        $this->assertNotRegExp($regex, '11');
    }

    public function testBlockTagName(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_BLOCKTAGNAME . '$/';
        $this->assertRegExp($regex, 'p');
        $this->assertRegExp($regex, 'div');
        $this->assertRegExp($regex, 'h1');
        $this->assertNotRegExp($regex, 'a');
        $this->assertNotRegExp($regex, 'h7');
    }

    public function testAttributeName(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_ATTRIBUTENAME . '$/';
        $this->assertRegExp($regex, 'href');
        $this->assertRegExp($regex, 'class');
        $this->assertRegExp($regex, 'data-src');
        $this->assertNotRegExp($regex, '-key');
    }

    public function testUnquotedValue(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_UNQUOTEDVALUE . '$/';
        $this->assertRegExp($regex, 'foo');
        $this->assertRegExp($regex, 'bar');
        $this->assertNotRegExp($regex, '"baz"');
    }

    public function testSingleQuotedValue(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_SINGLEQUOTEDVALUE . '$/';
        $this->assertRegExp($regex, '\'foo\'');
        $this->assertRegExp($regex, '\'bar\'');
        $this->assertNotRegExp($regex, '"baz"');
    }

    public function testDoubleQuotedValue(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_DOUBLEQUOTEDVALUE . '$/';
        $this->assertRegExp($regex, '"foo"');
        $this->assertRegExp($regex, '"bar"');
        $this->assertNotRegExp($regex, '\'baz\'');
    }

    public function testAttributeValue(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_ATTRIBUTEVALUE . '$/';
        $this->assertRegExp($regex, 'foo');
        $this->assertRegExp($regex, '\'bar\'');
        $this->assertRegExp($regex, '"baz"');
    }

    public function testAttributeValueSpec(): void
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

    public function testAttribute(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_ATTRIBUTE . '$/';
        $this->assertRegExp($regex, ' disabled');
        $this->assertRegExp($regex, ' disabled="disabled"');
        $this->assertRegExp($regex, ' href="http://www.google.com"');
        $this->assertNotRegExp($regex, 'disabled', 'There must be at least one space at the start');
    }

    public function testOpenTag(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_OPENTAG . '$/';
        $this->assertRegExp($regex, '<hr>');
        $this->assertRegExp($regex, '<a href="http://www.google.com">');
        $this->assertRegExp($regex, '<img src="http://www.google.com/logo.png" />');
        $this->assertNotRegExp($regex, '</p>');
    }

    public function testCloseTag(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_CLOSETAG . '$/';
        $this->assertRegExp($regex, '</p>');
        $this->assertRegExp($regex, '</a>');
        $this->assertNotRegExp($regex, '<hr>');
        $this->assertNotRegExp($regex, '<img src="http://www.google.com/logo.png" />');
    }

    public function testOpenBlockTag(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_OPENBLOCKTAG . '$/';
        $this->assertRegExp($regex, '<body>');
        $this->assertRegExp($regex, '<hr>');
        $this->assertRegExp($regex, '<hr />');
        $this->assertRegExp($regex, '<p id="foo" class="bar">');
        $this->assertNotRegExp($regex, '<a href="http://www.google.com">', 'This is not a block element');
        $this->assertNotRegExp($regex, '</p>', 'This is not an opening tag');
    }

    public function testCloseBlockTag(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_CLOSEBLOCKTAG . '$/';
        $this->assertRegExp($regex, '</body>');
        $this->assertRegExp($regex, '</p>');
        $this->assertNotRegExp($regex, '</a>', 'This is not a block element');
        $this->assertNotRegExp($regex, '<br>', 'This is not a closing tag');
    }

    public function testHtmlComment(): void
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

    public function testProcessingInstruction(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_PROCESSINGINSTRUCTION . '$/';
        $this->assertRegExp($regex, '<?PITarget PIContent?>');
        $this->assertRegExp($regex, '<?xml-stylesheet type="text/xsl" href="style.xsl"?>');
    }

    public function testDeclaration(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_DECLARATION . '$/';
        $this->assertRegExp($regex, '<!DOCTYPE html>');
        $this->assertRegExp($regex, '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">');
        $this->assertRegExp($regex, '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
    }

    public function testCDATA(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_CDATA . '$/';
        $this->assertRegExp($regex, '<![CDATA[<sender>John Smith</sender>]]>');
        $this->assertRegExp($regex, '<![CDATA[]]]]><![CDATA[>]]>');
    }

    public function testHtmlTag(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_HTMLTAG . '$/';
        $this->assertRegExp($regex, '<body id="main">');
        $this->assertRegExp($regex, '</p>');
        $this->assertRegExp($regex, '<!-- HELLO WORLD -->');
        $this->assertRegExp($regex, '<?xml-stylesheet type="text/xsl" href="style.xsl"?>');
        $this->assertRegExp($regex, '<!DOCTYPE html>');
        $this->assertRegExp($regex, '<![CDATA[<sender>John Smith</sender>]]>');
    }

    public function testHtmlBlockOpen(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_HTMLBLOCKOPEN . '$/';
        $this->assertRegExp($regex, '<h1>');
        $this->assertRegExp($regex, '</p>');
    }

    public function testLinkTitle(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_HTMLBLOCKOPEN . '$/';
        $this->assertRegExp($regex, '<h1>');
        $this->assertRegExp($regex, '</p>');
    }

    public function testUnescape(): void
    {
        $this->assertEquals('foo(and(bar))', RegexHelper::unescape('foo(and\\(bar\\))'));
    }

    public function testIsEscapable(): void
    {
        $this->assertFalse(RegexHelper::isEscapable(''));
        $this->assertFalse(RegexHelper::isEscapable('A'));
        $this->assertTrue(RegexHelper::isEscapable('\\'));
    }

    /**
     * @dataProvider dataForTestMatchAt
     */
    public function testMatchAt(string $regex, string $string, ?int $offset, int $expectedResult): void
    {
        if ($offset === null) {
            $this->assertEquals($expectedResult, RegexHelper::matchAt($regex, $string));
        } else {
            $this->assertEquals($expectedResult, RegexHelper::matchAt($regex, $string, $offset));
        }
    }

    /**
     * @return iterable<array<mixed>>
     */
    public function dataForTestMatchAt(): iterable
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
     * @dataProvider blockTypesWithValidOpenerRegexes
     */
    public function testValidHtmlBlockOpenRegex(int $type): void
    {
        $this->assertNotEmpty(RegexHelper::getHtmlBlockOpenRegex($type));
    }

    /**
     * @return iterable<int>
     */
    public function blockTypesWithValidOpenerRegexes(): iterable
    {
        yield [HtmlBlock::TYPE_1_CODE_CONTAINER];
        yield [HtmlBlock::TYPE_2_COMMENT];
        yield [HtmlBlock::TYPE_3];
        yield [HtmlBlock::TYPE_4];
        yield [HtmlBlock::TYPE_5_CDATA];
        yield [HtmlBlock::TYPE_6_BLOCK_ELEMENT];
        yield [HtmlBlock::TYPE_7_MISC_ELEMENT];
    }

    public function testInvalidHtmlBlockOpenRegex(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        RegexHelper::getHtmlBlockOpenRegex(8);
    }

    /**
     * @dataProvider blockTypesWithValidCloserRegexes
     */
    public function testValidHtmlBlockCloseRegex(int $type): void
    {
        $this->assertNotEmpty(RegexHelper::getHtmlBlockOpenRegex($type));
    }

    /**
     * @return iterable<int>
     */
    public function blockTypesWithValidCloserRegexes(): iterable
    {
        yield [HtmlBlock::TYPE_1_CODE_CONTAINER];
        yield [HtmlBlock::TYPE_2_COMMENT];
        yield [HtmlBlock::TYPE_3];
        yield [HtmlBlock::TYPE_4];
        yield [HtmlBlock::TYPE_5_CDATA];
    }

    /**
     * @dataProvider blockTypesWithInvalidCloserRegexes
     */
    public function testInvalidHtmlBlockCloseRegex(int $type): void
    {
        $this->expectException(\InvalidArgumentException::class);

        RegexHelper::getHtmlBlockCloseRegex($type);
    }

    /**
     * @return iterable<int>
     */
    public function blockTypesWithInvalidCloserRegexes(): iterable
    {
        yield [HtmlBlock::TYPE_6_BLOCK_ELEMENT];
        yield [HtmlBlock::TYPE_7_MISC_ELEMENT];
        yield [8];
    }
}
