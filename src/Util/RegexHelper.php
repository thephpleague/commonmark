<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Util;

use League\CommonMark\Block\Element\HtmlBlock;

/**
 * Provides regular expressions and utilities for parsing Markdown
 */
final class RegexHelper
{
    /** @deprecated Use PARTIAL_ESCAPABLE instead */
    const ESCAPABLE = 0;

    /** @deprecated Use PARTIAL_ESCAPED_CHAR instead */
    const ESCAPED_CHAR = 1;

    /** @deprecated Use PARTIAL_IN_DOUBLE_QUOTES instead */
    const IN_DOUBLE_QUOTES = 2;

    /** @deprecated Use PARTIAL_IN_SINGLE_QUOTES instead */
    const IN_SINGLE_QUOTES = 3;

    /** @deprecated Use PARTIAL_IN_PARENS instead */
    const IN_PARENS = 4;

    /** @deprecated Use PARTIAL_REG_CHAR instead */
    const REG_CHAR = 5;

    /** @deprecated Use PARTIAL_IN_PARENS_NOSP instead */
    const IN_PARENS_NOSP = 6;

    /** @deprecated Use PARTIAL_TAGNAME instead */
    const TAGNAME = 7;

    /** @deprecated Use PARTIAL_BLOCKTAGNAME instead */
    const BLOCKTAGNAME = 8;

    /** @deprecated Use PARTIAL_ATTRIBUTENAME instead */
    const ATTRIBUTENAME = 9;

    /** @deprecated Use PARTIAL_UNQUOTEDVALUE instead */
    const UNQUOTEDVALUE = 10;

    /** @deprecated Use PARTIAL_SINGLEQUOTEDVALUE instead */
    const SINGLEQUOTEDVALUE = 11;

    /** @deprecated Use PARTIAL_DOUBLEQUOTEDVALUE instead */
    const DOUBLEQUOTEDVALUE = 12;

    /** @deprecated Use PARTIAL_ATTRIBUTEVALUE instead */
    const ATTRIBUTEVALUE = 13;

    /** @deprecated Use PARTIAL_ATTRIBUTEVALUESPEC instead */
    const ATTRIBUTEVALUESPEC = 14;

    /** @deprecated Use PARTIAL_ATTRIBUTE instead */
    const ATTRIBUTE = 15;

    /** @deprecated Use PARTIAL_OPENTAG instead */
    const OPENTAG = 16;

    /** @deprecated Use PARTIAL_CLOSETAG instead */
    const CLOSETAG = 17;

    /** @deprecated Use PARTIAL_OPENBLOCKTAG instead */
    const OPENBLOCKTAG = 18;

    /** @deprecated Use PARTIAL_CLOSEBLOCKTAG instead */
    const CLOSEBLOCKTAG = 19;

    /** @deprecated Use PARTIAL_HTMLCOMMENT instead */
    const HTMLCOMMENT = 20;

    /** @deprecated Use PARTIAL_PROCESSINGINSTRUCTION instead */
    const PROCESSINGINSTRUCTION = 21;

    /** @deprecated Use PARTIAL_DECLARATION instead */
    const DECLARATION = 22;

    /** @deprecated Use PARTIAL_CDATA instead */
    const CDATA = 23;

    /** @deprecated Use PARTIAL_HTMLTAG instead */
    const HTMLTAG = 24;

    /** @deprecated Use PARTIAL_HTMLBLOCKOPEN instead */
    const HTMLBLOCKOPEN = 25;

    /** @deprecated Use PARTIAL_LINK_TITLE instead */
    const LINK_TITLE = 26;

    // Partial regular expressions (wrap with `/` on each side before use)
    const PARTIAL_ENTITY = '&(?:#x[a-f0-9]{1,8}|#[0-9]{1,8}|[a-z][a-z0-9]{1,31});';
    const PARTIAL_ESCAPABLE = '[!"#$%&\'()*+,.\/:;<=>?@[\\\\\]^_`{|}~-]';
    const PARTIAL_ESCAPED_CHAR = '\\\\' . self::PARTIAL_ESCAPABLE;
    const PARTIAL_IN_DOUBLE_QUOTES = '"(' . self::PARTIAL_ESCAPED_CHAR . '|[^"\x00])*"';
    const PARTIAL_IN_SINGLE_QUOTES = '\'(' . self::PARTIAL_ESCAPED_CHAR . '|[^\'\x00])*\'';
    const PARTIAL_IN_PARENS = '\\((' . self::PARTIAL_ESCAPED_CHAR . '|[^)\x00])*\\)';
    const PARTIAL_REG_CHAR = '[^\\\\()\x00-\x20]';
    const PARTIAL_IN_PARENS_NOSP = '\((' . self::PARTIAL_REG_CHAR . '|' . self::PARTIAL_ESCAPED_CHAR . '|\\\\)*\)';
    const PARTIAL_TAGNAME = '[A-Za-z][A-Za-z0-9-]*';
    const PARTIAL_BLOCKTAGNAME = '(?:address|article|aside|base|basefont|blockquote|body|caption|center|col|colgroup|dd|details|dialog|dir|div|dl|dt|fieldset|figcaption|figure|footer|form|frame|frameset|h1|head|header|hr|html|iframe|legend|li|link|main|menu|menuitem|meta|nav|noframes|ol|optgroup|option|p|param|section|source|title|summary|table|tbody|td|tfoot|th|thead|title|tr|track|ul)';
    const PARTIAL_ATTRIBUTENAME = '[a-zA-Z_:][a-zA-Z0-9:._-]*';
    const PARTIAL_UNQUOTEDVALUE = '[^"\'=<>`\x00-\x20]+';
    const PARTIAL_SINGLEQUOTEDVALUE = '\'[^\']*\'';
    const PARTIAL_DOUBLEQUOTEDVALUE = '"[^"]*"';
    const PARTIAL_ATTRIBUTEVALUE = '(?:' . self::PARTIAL_UNQUOTEDVALUE . '|' . self::PARTIAL_SINGLEQUOTEDVALUE . '|' . self::PARTIAL_DOUBLEQUOTEDVALUE . ')';
    const PARTIAL_ATTRIBUTEVALUESPEC = '(?:' . '\s*=' . '\s*' . self::PARTIAL_ATTRIBUTEVALUE . ')';
    const PARTIAL_ATTRIBUTE = '(?:' . '\s+' . self::PARTIAL_ATTRIBUTENAME . self::PARTIAL_ATTRIBUTEVALUESPEC . '?)';
    const PARTIAL_OPENTAG = '<' . self::PARTIAL_TAGNAME . self::PARTIAL_ATTRIBUTE . '*' . '\s*\/?>';
    const PARTIAL_CLOSETAG = '<\/' . self::PARTIAL_TAGNAME . '\s*[>]';
    const PARTIAL_OPENBLOCKTAG = '<' . self::PARTIAL_BLOCKTAGNAME . self::PARTIAL_ATTRIBUTE . '*' . '\s*\/?>';
    const PARTIAL_CLOSEBLOCKTAG = '<\/' . self::PARTIAL_BLOCKTAGNAME . '\s*[>]';
    const PARTIAL_HTMLCOMMENT = '<!---->|<!--(?:-?[^>-])(?:-?[^-])*-->';
    const PARTIAL_PROCESSINGINSTRUCTION = '[<][?].*?[?][>]';
    const PARTIAL_DECLARATION = '<![A-Z]+' . '\s+[^>]*>';
    const PARTIAL_CDATA = '<!\[CDATA\[[\s\S]*?]\]>';
    const PARTIAL_HTMLTAG = '(?:' . self::PARTIAL_OPENTAG . '|' . self::PARTIAL_CLOSETAG . '|' . self::PARTIAL_HTMLCOMMENT . '|' .
        self::PARTIAL_PROCESSINGINSTRUCTION . '|' . self::PARTIAL_DECLARATION . '|' . self::PARTIAL_CDATA . ')';
    const PARTIAL_HTMLBLOCKOPEN = '<(?:' . self::PARTIAL_BLOCKTAGNAME . '(?:[\s\/>]|$)' . '|' .
        '\/' . self::PARTIAL_BLOCKTAGNAME . '(?:[\s>]|$)' . '|' . '[?!])';
    const PARTIAL_LINK_TITLE = '^(?:"(' . self::PARTIAL_ESCAPED_CHAR . '|[^"\x00])*"' .
        '|' . '\'(' . self::PARTIAL_ESCAPED_CHAR . '|[^\'\x00])*\'' .
        '|' . '\((' . self::PARTIAL_ESCAPED_CHAR . '|[^)\x00])*\))';

    /** @deprecated Use PARTIAL_ESCAPABLE instead */
    const REGEX_ESCAPABLE = self::PARTIAL_ESCAPABLE;

    /** @deprecated Use PARTIAL_ENTITY instead */
    const REGEX_ENTITY = self::PARTIAL_ENTITY;

    const REGEX_PUNCTUATION = '/^[\x{2000}-\x{206F}\x{2E00}-\x{2E7F}\p{Pc}\p{Pd}\p{Pe}\p{Pf}\p{Pi}\p{Po}\p{Ps}\\\\\'!"#\$%&\(\)\*\+,\-\.\\/:;<=>\?@\[\]\^_`\{\|\}~]/u';
    const REGEX_UNSAFE_PROTOCOL = '/^javascript:|vbscript:|file:|data:/i';
    const REGEX_SAFE_DATA_PROTOCOL = '/^data:image\/(?:png|gif|jpeg|webp)/i';
    const REGEX_NON_SPACE = '/[^ \t\f\v\r\n]/';

    const REGEX_WHITESPACE_CHAR = '/^[ \t\n\x0b\x0c\x0d]/';
    const REGEX_WHITESPACE = '/[ \t\n\x0b\x0c\x0d]+/';
    const REGEX_UNICODE_WHITESPACE_CHAR = '/^\pZ|\s/u';
    const REGEX_THEMATIC_BREAK = '/^(?:(?:\*[ \t]*){3,}|(?:_[ \t]*){3,}|(?:-[ \t]*){3,})[ \t]*$/';
    const REGEX_LINK_DESTINATION_BRACES = '/^(?:' . '[<](?:[^ <>\\t\\n\\\\\\x00]' . '|' . self::PARTIAL_ESCAPED_CHAR . '|' . '\\\\)*[>]' . ')/';

    /**
     * @deprecated Instance methods will be removed in 0.18 or 1.0 (whichever comes first)
     */
    protected static $instance;

    /**
     * @return RegexHelper
     *
     * @deprecated Instances are no longer needed and will be removed in 0.18 or 1.0
     */
    public static function getInstance()
    {
        @trigger_error('RegexHelper no longer uses the singleton pattern. Directly grab the REGEX_ or PARTIAL_ constant you need instead.', E_USER_DEPRECATED);

        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param string|null $character
     *
     * @return bool
     */
    public static function isEscapable($character)
    {
        if ($character === null) {
            return false;
        }

        return preg_match('/' . self::PARTIAL_ESCAPABLE . '/', $character) === 1;
    }

    /**
     * Returns a partial regex
     *
     * It'll need to be wrapped with /.../ before use
     *
     * @param int $const
     *
     * @return string
     *
     * @deprecated Just grab the constant directly
     */
    public function getPartialRegex($const)
    {
        @trigger_error('RegexHelper no longer supports the getPartialRegex() function. Directly grab the PARTIAL_ constant you need instead.', E_USER_DEPRECATED);

        switch ($const) {
            case self::ESCAPABLE: return self::PARTIAL_ESCAPABLE;
            case self::ESCAPED_CHAR: return self::PARTIAL_ESCAPED_CHAR;
            case self::IN_DOUBLE_QUOTES: return self::PARTIAL_IN_DOUBLE_QUOTES;
            case self::IN_SINGLE_QUOTES: return self::PARTIAL_IN_SINGLE_QUOTES;
            case self::IN_PARENS: return self::PARTIAL_IN_PARENS;
            case self::REG_CHAR: return self::PARTIAL_REG_CHAR;
            case self::IN_PARENS_NOSP: return self::PARTIAL_IN_PARENS_NOSP;
            case self::TAGNAME: return self::PARTIAL_TAGNAME;
            case self::BLOCKTAGNAME: return self::PARTIAL_BLOCKTAGNAME;
            case self::ATTRIBUTENAME: return self::PARTIAL_ATTRIBUTENAME;
            case self::UNQUOTEDVALUE: return self::PARTIAL_UNQUOTEDVALUE;
            case self::SINGLEQUOTEDVALUE: return self::PARTIAL_SINGLEQUOTEDVALUE;
            case self::DOUBLEQUOTEDVALUE: return self::PARTIAL_DOUBLEQUOTEDVALUE;
            case self::ATTRIBUTEVALUE: return self::PARTIAL_ATTRIBUTEVALUE;
            case self::ATTRIBUTEVALUESPEC: return self::PARTIAL_ATTRIBUTEVALUESPEC;
            case self::ATTRIBUTE: return self::PARTIAL_ATTRIBUTE;
            case self::OPENTAG: return self::PARTIAL_OPENTAG;
            case self::CLOSETAG: return self::PARTIAL_CLOSETAG;
            case self::OPENBLOCKTAG: return self::PARTIAL_OPENBLOCKTAG;
            case self::CLOSEBLOCKTAG: return self::PARTIAL_CLOSEBLOCKTAG;
            case self::HTMLCOMMENT: return self::PARTIAL_HTMLCOMMENT;
            case self::PROCESSINGINSTRUCTION: return self::PARTIAL_PROCESSINGINSTRUCTION;
            case self::DECLARATION: return self::PARTIAL_DECLARATION;
            case self::CDATA: return self::PARTIAL_CDATA;
            case self::HTMLTAG: return self::PARTIAL_HTMLTAG;
            case self::HTMLBLOCKOPEN: return self::PARTIAL_HTMLBLOCKOPEN;
            case self::LINK_TITLE: return self::PARTIAL_LINK_TITLE;
        }
    }

    /**
     * @return string
     *
     * @deprecated Use PARTIAL_HTMLTAG and wrap it yourself instead
     */
    public function getHtmlTagRegex()
    {
        @trigger_error('RegexHelper::getHtmlTagRegex() has been deprecated. Use the RegexHelper::PARTIAL_HTMLTAG constant instead.', E_USER_DEPRECATED);

        return '/^' . self::PARTIAL_HTMLTAG . '/i';
    }

    /**
     * @return string
     *
     * @deprecated Use PARTIAL_LINK_TITLE and wrap it yourself instead
     */
    public function getLinkTitleRegex()
    {
        @trigger_error('RegexHelper::getLinkTitleRegex() has been deprecated. Use the RegexHelper::PARTIAL_LINK_TITLE constant instead.', E_USER_DEPRECATED);

        return '/' . self::PARTIAL_LINK_TITLE . '/';
    }

    /**
     * @return string
     *
     * @deprecated Use REGEX_LINK_DESTINATION_BRACES instead
     */
    public function getLinkDestinationBracesRegex()
    {
        @trigger_error('RegexHelper::getLinkDestinationBracesRegex() has been deprecated. Use the RegexHelper::REGEX_LINK_DESTINATION_BRACES constant instead.', E_USER_DEPRECATED);

        return self::REGEX_LINK_DESTINATION_BRACES;
    }

    /**
     * @return string
     *
     * @deprecated Use the REGEX_THEMATIC_BREAK constant directly
     */
    public function getThematicBreakRegex()
    {
        @trigger_error('RegexHelper::getThematicBreakRegex() has been deprecated. Use the RegexHelper::REGEX_THEMATIC_BREAK constant instead.', E_USER_DEPRECATED);

        return self::REGEX_THEMATIC_BREAK;
    }

    /**
     * Attempt to match a regex in string s at offset offset
     *
     * @param string $regex
     * @param string $string
     * @param int    $offset
     *
     * @return int|null Index of match, or null
     */
    public static function matchAt($regex, $string, $offset = 0)
    {
        $matches = [];
        $string = mb_substr($string, $offset, null, 'utf-8');
        if (!preg_match($regex, $string, $matches, PREG_OFFSET_CAPTURE)) {
            return;
        }

        // PREG_OFFSET_CAPTURE always returns the byte offset, not the char offset, which is annoying
        $charPos = mb_strlen(mb_strcut($string, 0, $matches[0][1], 'utf-8'), 'utf-8');

        return $offset + $charPos;
    }

    /**
     * Functional wrapper around preg_match_all
     *
     * @param string $pattern
     * @param string $subject
     * @param int    $offset
     *
     * @return array|null
     */
    public static function matchAll($pattern, $subject, $offset = 0)
    {
        $matches = [];
        $subject = substr($subject, $offset);
        preg_match_all($pattern, $subject, $matches, PREG_PATTERN_ORDER);

        $fullMatches = reset($matches);
        if (empty($fullMatches)) {
            return;
        }

        if (count($fullMatches) === 1) {
            foreach ($matches as &$match) {
                $match = reset($match);
            }
        }

        if (!empty($matches)) {
            return $matches;
        }
    }

    /**
     * Replace backslash escapes with literal characters
     *
     * @param string $string
     *
     * @return string
     */
    public static function unescape($string)
    {
        $allEscapedChar = '/\\\\(' . self::PARTIAL_ESCAPABLE . ')/';

        $escaped = preg_replace($allEscapedChar, '$1', $string);
        $replaced = preg_replace_callback('/' . self::PARTIAL_ENTITY . '/i', function ($e) {
            return Html5Entities::decodeEntity($e[0]);
        }, $escaped);

        return $replaced;
    }

    /**
     * @param int $type HTML block type
     *
     * @return string|null
     */
    public static function getHtmlBlockOpenRegex($type)
    {
        switch ($type) {
            case HtmlBlock::TYPE_1_CODE_CONTAINER:
                return '/^<(?:script|pre|style)(?:\s|>|$)/i';
            case HtmlBlock::TYPE_2_COMMENT:
                return '/^<!--/';
            case HtmlBlock::TYPE_3:
                return '/^<[?]/';
            case HtmlBlock::TYPE_4:
                return '/^<![A-Z]/';
            case HtmlBlock::TYPE_5_CDATA:
                return '/^<!\[CDATA\[/';
            case HtmlBlock::TYPE_6_BLOCK_ELEMENT:
                return '%^<[/]?(?:address|article|aside|base|basefont|blockquote|body|caption|center|col|colgroup|dd|details|dialog|dir|div|dl|dt|fieldset|figcaption|figure|footer|form|frame|frameset|h[123456]|head|header|hr|html|iframe|legend|li|link|main|menu|menuitem|meta|nav|noframes|ol|optgroup|option|p|param|section|source|title|summary|table|tbody|td|tfoot|th|thead|title|tr|track|ul)(?:\s|[/]?[>]|$)%i';
            case HtmlBlock::TYPE_7_MISC_ELEMENT:
                return '/^(?:' . self::PARTIAL_OPENTAG . '|' . self::PARTIAL_CLOSETAG . ')\\s*$/i';
        }
    }

    /**
     * @param int $type HTML block type
     *
     * @return string|null
     */
    public static function getHtmlBlockCloseRegex($type)
    {
        switch ($type) {
            case HtmlBlock::TYPE_1_CODE_CONTAINER:
                return '%<\/(?:script|pre|style)>%i';
            case HtmlBlock::TYPE_2_COMMENT:
                return '/-->/';
            case HtmlBlock::TYPE_3:
                return '/\?>/';
            case HtmlBlock::TYPE_4:
                return '/>/';
            case HtmlBlock::TYPE_5_CDATA:
                return '/\]\]>/';
        }
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    public static function isLinkPotentiallyUnsafe($url)
    {
        return preg_match(self::REGEX_UNSAFE_PROTOCOL, $url) !== 0 && preg_match(self::REGEX_SAFE_DATA_PROTOCOL, $url) === 0;
    }
}
