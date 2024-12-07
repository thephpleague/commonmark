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

use League\CommonMark\Exception\InvalidArgumentException;
use League\CommonMark\Extension\CommonMark\Node\Block\HtmlBlock;
use League\CommonMark\Util\RegexHelper;
use PHPUnit\Framework\TestCase;

/**
 * Tests the different regular expressions
 */
final class RegexHelperTest extends TestCase
{
    public function testEscapable(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_ESCAPABLE . '$/';
        $this->assertRegexMatches($regex, '&');
        $this->assertRegexMatches($regex, '/');
        $this->assertRegexMatches($regex, '\\');
        $this->assertRegexMatches($regex, '(');
        $this->assertRegexMatches($regex, ')');
    }

    public function testEscapedChar(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_ESCAPED_CHAR . '$/';
        $this->assertRegexMatches($regex, '\\&');
        $this->assertRegexMatches($regex, '\\/');
        $this->assertRegexMatches($regex, '\\\\');
        $this->assertRegexMatches($regex, '\)');
        $this->assertRegexMatches($regex, '\(');
    }

    public function testInDoubleQuotes(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_IN_DOUBLE_QUOTES . '$/';
        $this->assertRegexMatches($regex, '"\\&"');
        $this->assertRegexMatches($regex, '"\\/"');
        $this->assertRegexMatches($regex, '"\\\\"');
    }

    public function testInSingleQuotes(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_IN_SINGLE_QUOTES . '$/';
        $this->assertRegexMatches($regex, '\'\\&\'');
        $this->assertRegexMatches($regex, '\'\\/\'');
        $this->assertRegexMatches($regex, '\'\\\\\'');
    }

    public function testInParens(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_IN_PARENS . '$/';
        $this->assertRegexMatches($regex, '(\\&)');
        $this->assertRegexMatches($regex, '(\\/)');
        $this->assertRegexMatches($regex, '(\\\\)');
    }

    public function testRegChar(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_REG_CHAR . '$/';
        $this->assertRegexMatches($regex, 'a');
        $this->assertRegexMatches($regex, 'A');
        $this->assertRegexMatches($regex, '!');
        $this->assertRegexDoesNotMatch($regex, ' ');
    }

    public function testInParensNoSp(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_IN_PARENS_NOSP . '$/';
        $this->assertRegexMatches($regex, '(a)');
        $this->assertRegexMatches($regex, '(A)');
        $this->assertRegexMatches($regex, '(!)');
        $this->assertRegexDoesNotMatch($regex, '(a )');
    }

    public function testTagname(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_TAGNAME . '$/';
        $this->assertRegexMatches($regex, 'a');
        $this->assertRegexMatches($regex, 'img');
        $this->assertRegexMatches($regex, 'h1');
        $this->assertRegexDoesNotMatch($regex, '11');
    }

    public function testBlockTagName(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_BLOCKTAGNAME . '$/';
        $this->assertRegexMatches($regex, 'p');
        $this->assertRegexMatches($regex, 'div');
        $this->assertRegexMatches($regex, 'h1');
        $this->assertRegexDoesNotMatch($regex, 'a');
        $this->assertRegexDoesNotMatch($regex, 'h7');
    }

    public function testAttributeName(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_ATTRIBUTENAME . '$/';
        $this->assertRegexMatches($regex, 'href');
        $this->assertRegexMatches($regex, 'class');
        $this->assertRegexMatches($regex, 'data-src');
        $this->assertRegexDoesNotMatch($regex, '-key');
    }

    public function testUnquotedValue(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_UNQUOTEDVALUE . '$/';
        $this->assertRegexMatches($regex, 'foo');
        $this->assertRegexMatches($regex, 'bar');
        $this->assertRegexDoesNotMatch($regex, '"baz"');
    }

    public function testSingleQuotedValue(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_SINGLEQUOTEDVALUE . '$/';
        $this->assertRegexMatches($regex, '\'foo\'');
        $this->assertRegexMatches($regex, '\'bar\'');
        $this->assertRegexDoesNotMatch($regex, '"baz"');
    }

    public function testDoubleQuotedValue(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_DOUBLEQUOTEDVALUE . '$/';
        $this->assertRegexMatches($regex, '"foo"');
        $this->assertRegexMatches($regex, '"bar"');
        $this->assertRegexDoesNotMatch($regex, '\'baz\'');
    }

    public function testAttributeValue(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_ATTRIBUTEVALUE . '$/';
        $this->assertRegexMatches($regex, 'foo');
        $this->assertRegexMatches($regex, '\'bar\'');
        $this->assertRegexMatches($regex, '"baz"');
    }

    public function testAttributeValueSpec(): void
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

    public function testAttribute(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_ATTRIBUTE . '$/';
        $this->assertRegexMatches($regex, ' disabled');
        $this->assertRegexMatches($regex, ' disabled="disabled"');
        $this->assertRegexMatches($regex, ' href="http://www.google.com"');
        $this->assertRegexDoesNotMatch($regex, 'disabled', 'There must be at least one space at the start');
    }

    public function testOpenTag(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_OPENTAG . '$/';
        $this->assertRegexMatches($regex, '<hr>');
        $this->assertRegexMatches($regex, '<a href="http://www.google.com">');
        $this->assertRegexMatches($regex, '<img src="http://www.google.com/logo.png" />');
        $this->assertRegexDoesNotMatch($regex, '</p>');
    }

    public function testCloseTag(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_CLOSETAG . '$/';
        $this->assertRegexMatches($regex, '</p>');
        $this->assertRegexMatches($regex, '</a>');
        $this->assertRegexDoesNotMatch($regex, '<hr>');
        $this->assertRegexDoesNotMatch($regex, '<img src="http://www.google.com/logo.png" />');
    }

    public function testOpenBlockTag(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_OPENBLOCKTAG . '$/';
        $this->assertRegexMatches($regex, '<body>');
        $this->assertRegexMatches($regex, '<hr>');
        $this->assertRegexMatches($regex, '<hr />');
        $this->assertRegexMatches($regex, '<p id="foo" class="bar">');
        $this->assertRegexDoesNotMatch($regex, '<a href="http://www.google.com">', 'This is not a block element');
        $this->assertRegexDoesNotMatch($regex, '</p>', 'This is not an opening tag');
    }

    public function testCloseBlockTag(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_CLOSEBLOCKTAG . '$/';
        $this->assertRegexMatches($regex, '</body>');
        $this->assertRegexMatches($regex, '</p>');
        $this->assertRegexDoesNotMatch($regex, '</a>', 'This is not a block element');
        $this->assertRegexDoesNotMatch($regex, '<br>', 'This is not a closing tag');
    }

    public function testHtmlComment(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_HTMLCOMMENT . '$/';
        $this->assertRegexMatches($regex, '<!---->');
        $this->assertRegexMatches($regex, '<!-- -->');
        $this->assertRegexMatches($regex, '<!-- HELLO WORLD -->');
        $this->assertRegexMatches($regex, '<!-->');
        $this->assertRegexMatches($regex, '<!--->');
        $this->assertRegexDoesNotMatch($regex, '<!->');
        $this->assertRegexDoesNotMatch($regex, '<!- ->');
    }

    public function testProcessingInstruction(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_PROCESSINGINSTRUCTION . '$/';
        $this->assertRegexMatches($regex, '<?PITarget PIContent?>');
        $this->assertRegexMatches($regex, '<?xml-stylesheet type="text/xsl" href="style.xsl"?>');
    }

    public function testDeclaration(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_DECLARATION . '$/';
        $this->assertRegexMatches($regex, '<!DOCTYPE html>');
        $this->assertRegexMatches($regex, '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">');
        $this->assertRegexMatches($regex, '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
    }

    public function testCDATA(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_CDATA . '$/';
        $this->assertRegexMatches($regex, '<![CDATA[<sender>John Smith</sender>]]>');
        $this->assertRegexMatches($regex, '<![CDATA[]]]]><![CDATA[>]]>');
    }

    public function testHtmlTag(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_HTMLTAG . '$/';
        $this->assertRegexMatches($regex, '<body id="main">');
        $this->assertRegexMatches($regex, '</p>');
        $this->assertRegexMatches($regex, '<!-- HELLO WORLD -->');
        $this->assertRegexMatches($regex, '<?xml-stylesheet type="text/xsl" href="style.xsl"?>');
        $this->assertRegexMatches($regex, '<!DOCTYPE html>');
        $this->assertRegexMatches($regex, '<![CDATA[<sender>John Smith</sender>]]>');
        $this->assertRegexDoesNotMatch($regex, '<![cdata[<sender>John Smith</sender>]]>');
    }

    public function testHtmlBlockOpen(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_HTMLBLOCKOPEN . '$/';
        $this->assertRegexMatches($regex, '<h1>');
        $this->assertRegexMatches($regex, '</p>');
    }

    public function testLinkTitle(): void
    {
        $regex = '/^' . RegexHelper::PARTIAL_HTMLBLOCKOPEN . '$/';
        $this->assertRegexMatches($regex, '<h1>');
        $this->assertRegexMatches($regex, '</p>');
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

    public function testIsWhitespace(): void
    {
        $this->assertFalse(RegexHelper::isWhitespace(''));
        $this->assertFalse(RegexHelper::isWhitespace('A'));
        $this->assertTrue(RegexHelper::isWhitespace(' '));
        $this->assertTrue(RegexHelper::isWhitespace("\t"));
        $this->assertTrue(RegexHelper::isWhitespace("\n"));
        $this->assertTrue(RegexHelper::isWhitespace("\r"));
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
    public static function dataForTestMatchAt(): iterable
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

    public function testMatchFirst(): void
    {
        $this->assertSame(null, RegexHelper::matchFirst('/^bar/', 'foobar'));
        $this->assertSame(['bar'], RegexHelper::matchFirst('/^bar/', 'foobar', 3));
        $this->assertSame(['bar', 'bar'], RegexHelper::matchFirst('/^(bar)/', 'foobar', 3));
        $this->assertSame(['bar', 'ar'], RegexHelper::matchFirst('/^b(.+)/', 'foobar', 3));

        $this->assertSame(['brown fox', 'brown', 'fox'], RegexHelper::matchFirst('/(quick|brown|lazy) (fox|dog)/', 'The quick brown fox jumps over the lazy dog'));
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
    public static function blockTypesWithValidOpenerRegexes(): iterable
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
        $this->expectException(InvalidArgumentException::class);

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
    public static function blockTypesWithValidCloserRegexes(): iterable
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
        $this->expectException(InvalidArgumentException::class);

        RegexHelper::getHtmlBlockCloseRegex($type);
    }

    /**
     * @return iterable<int>
     */
    public static function blockTypesWithInvalidCloserRegexes(): iterable
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
