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
 * Provides regular expressions and utilties for parsing Markdown
 *
 * Singletons are generally bad, but it allows us to build the regexes once (and only once).
 */
class RegexHelper
{
    const ESCAPABLE = 0;
    const ESCAPED_CHAR = 1;
    const IN_DOUBLE_QUOTES = 2;
    const IN_SINGLE_QUOTES = 3;
    const IN_PARENS = 4;
    const REG_CHAR = 5;
    const IN_PARENS_NOSP = 6;
    const TAGNAME = 7;
    const BLOCKTAGNAME = 8;
    const ATTRIBUTENAME = 9;
    const UNQUOTEDVALUE = 10;
    const SINGLEQUOTEDVALUE = 11;
    const DOUBLEQUOTEDVALUE = 12;
    const ATTRIBUTEVALUE = 13;
    const ATTRIBUTEVALUESPEC = 14;
    const ATTRIBUTE = 15;
    const OPENTAG = 16;
    const CLOSETAG = 17;
    const OPENBLOCKTAG = 18;
    const CLOSEBLOCKTAG = 19;
    const HTMLCOMMENT = 20;
    const PROCESSINGINSTRUCTION = 21;
    const DECLARATION = 22;
    const CDATA = 23;
    const HTMLTAG = 24;
    const HTMLBLOCKOPEN = 25;
    const LINK_TITLE = 26;

    const REGEX_ESCAPABLE = '[!"#$%&\'()*+,.\/:;<=>?@[\\\\\]^_`{|}~-]';
    const REGEX_ENTITY = '&(?:#x[a-f0-9]{1,8}|#[0-9]{1,8}|[a-z][a-z0-9]{1,31});';
    const REGEX_PUNCTUATION = '/^[\x{2000}-\x{206F}\x{2E00}-\x{2E7F}\p{Pc}\p{Pd}\p{Pe}\p{Pf}\p{Pi}\p{Po}\p{Ps}\\\\\'!"#\$%&\(\)\*\+,\-\.\\/:;<=>\?@\[\]\^_`\{\|\}~]/u';
    const REGEX_UNSAFE_PROTOCOL = '/^javascript:|vbscript:|file:|data:/i';
    const REGEX_SAFE_DATA_PROTOCOL = '/^data:image\/(?:png|gif|jpeg|webp)/i';
    const REGEX_NON_SPACE = '/[^ \t\f\v\r\n]/';

    const REGEX_WHITESPACE_CHAR = '/^[ \t\n\x0b\x0c\x0d]/';
    const REGEX_WHITESPACE = '/[ \t\n\x0b\x0c\x0d]+/';
    const REGEX_UNICODE_WHITESPACE_CHAR = '/^\pZ|\s/u';

    /**
     * @deprecated
     */
    const REGEX_UNICODE_WHITESPACE = '/\pZ|\s/u';

    protected $regex = [];

    protected static $instance;

    /**
     * Constructor
     */
    protected function __construct()
    {
        $this->buildRegexPatterns();
    }

    /**
     * @return RegexHelper
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Builds the regular expressions required to parse Markdown
     *
     * We could hard-code them all as pre-built constants, but that would be more difficult to manage.
     */
    protected function buildRegexPatterns()
    {
        $regex = [];
        $regex[self::ESCAPABLE] = self::REGEX_ESCAPABLE;
        $regex[self::ESCAPED_CHAR] = '\\\\' . $regex[self::ESCAPABLE];
        $regex[self::IN_DOUBLE_QUOTES] = '"(' . $regex[self::ESCAPED_CHAR] . '|[^"\x00])*"';
        $regex[self::IN_SINGLE_QUOTES] = '\'(' . $regex[self::ESCAPED_CHAR] . '|[^\'\x00])*\'';
        $regex[self::IN_PARENS] = '\\((' . $regex[self::ESCAPED_CHAR] . '|[^)\x00])*\\)';
        $regex[self::REG_CHAR] = '[^\\\\()\x00-\x20]';
        $regex[self::IN_PARENS_NOSP] = '\((' . $regex[self::REG_CHAR] . '|' . $regex[self::ESCAPED_CHAR] . '|\\\\)*\)';
        $regex[self::TAGNAME] = '[A-Za-z][A-Za-z0-9-]*';
        $regex[self::BLOCKTAGNAME] = '(?:address|article|aside|base|basefont|blockquote|body|caption|center|col|colgroup|dd|details|dialog|dir|div|dl|dt|fieldset|figcaption|figure|footer|form|frame|frameset|h1|head|header|hr|html|iframe|legend|li|link|main|menu|menuitem|meta|nav|noframes|ol|optgroup|option|p|param|section|source|title|summary|table|tbody|td|tfoot|th|thead|title|tr|track|ul)';
        $regex[self::ATTRIBUTENAME] = '[a-zA-Z_:][a-zA-Z0-9:._-]*';
        $regex[self::UNQUOTEDVALUE] = '[^"\'=<>`\x00-\x20]+';
        $regex[self::SINGLEQUOTEDVALUE] = '\'[^\']*\'';
        $regex[self::DOUBLEQUOTEDVALUE] = '"[^"]*"';
        $regex[self::ATTRIBUTEVALUE] = '(?:' . $regex[self::UNQUOTEDVALUE] . '|' . $regex[self::SINGLEQUOTEDVALUE] . '|' . $regex[self::DOUBLEQUOTEDVALUE] . ')';
        $regex[self::ATTRIBUTEVALUESPEC] = '(?:' . '\s*=' . '\s*' . $regex[self::ATTRIBUTEVALUE] . ')';
        $regex[self::ATTRIBUTE] = '(?:' . '\s+' . $regex[self::ATTRIBUTENAME] . $regex[self::ATTRIBUTEVALUESPEC] . '?)';
        $regex[self::OPENTAG] = '<' . $regex[self::TAGNAME] . $regex[self::ATTRIBUTE] . '*' . '\s*\/?>';
        $regex[self::CLOSETAG] = '<\/' . $regex[self::TAGNAME] . '\s*[>]';
        $regex[self::OPENBLOCKTAG] = '<' . $regex[self::BLOCKTAGNAME] . $regex[self::ATTRIBUTE] . '*' . '\s*\/?>';
        $regex[self::CLOSEBLOCKTAG] = '<\/' . $regex[self::BLOCKTAGNAME] . '\s*[>]';
        $regex[self::HTMLCOMMENT] = '<!---->|<!--(?:-?[^>-])(?:-?[^-])*-->';
        $regex[self::PROCESSINGINSTRUCTION] = '[<][?].*?[?][>]';
        $regex[self::DECLARATION] = '<![A-Z]+' . '\s+[^>]*>';
        $regex[self::CDATA] = '<!\[CDATA\[[\s\S]*?]\]>';
        $regex[self::HTMLTAG] = '(?:' . $regex[self::OPENTAG] . '|' . $regex[self::CLOSETAG] . '|' . $regex[self::HTMLCOMMENT] . '|' .
            $regex[self::PROCESSINGINSTRUCTION] . '|' . $regex[self::DECLARATION] . '|' . $regex[self::CDATA] . ')';
        $regex[self::HTMLBLOCKOPEN] = '<(?:' . $regex[self::BLOCKTAGNAME] . '(?:[\s\/>]|$)' . '|' .
            '\/' . $regex[self::BLOCKTAGNAME] . '(?:[\s>]|$)' . '|' . '[?!])';
        $regex[self::LINK_TITLE] = '^(?:"(' . $regex[self::ESCAPED_CHAR] . '|[^"\x00])*"' .
            '|' . '\'(' . $regex[self::ESCAPED_CHAR] . '|[^\'\x00])*\'' .
            '|' . '\((' . $regex[self::ESCAPED_CHAR] . '|[^)\x00])*\))';

        $this->regex = $regex;
    }

    /**
     * Returns a partial regex
     *
     * It'll need to be wrapped with /.../ before use
     *
     * @param int $const
     *
     * @return string
     */
    public function getPartialRegex($const)
    {
        return $this->regex[$const];
    }

    /**
     * @return string
     */
    public function getHtmlTagRegex()
    {
        return '/^' . $this->regex[self::HTMLTAG] . '/i';
    }

    /**
     * @return string
     */
    public function getLinkTitleRegex()
    {
        return '/' . $this->regex[self::LINK_TITLE] . '/';
    }

    /**
     * @return string
     *
     * @deprecated
     */
    public function getLinkDestinationRegex()
    {
        @trigger_error('RegexHelper::getLinkDestinationRegex() is no longer used and will be removed in a future 0.x release.', E_USER_DEPRECATED);

        return '/^' . '(?:' . $this->regex[self::REG_CHAR] . '+|' . $this->regex[self::ESCAPED_CHAR] . '|\\\\|' . $this->regex[self::IN_PARENS_NOSP] . ')*' . '/';
    }

    /**
     * @return string
     */
    public function getLinkDestinationBracesRegex()
    {
        return '/^(?:' . '[<](?:[^ <>\\t\\n\\\\\\x00]' . '|' . $this->regex[self::ESCAPED_CHAR] . '|' . '\\\\)*[>]' . ')/';
    }

    /**
     * @return string
     */
    public function getThematicBreakRegex()
    {
        return '/^(?:(?:\*[ \t]*){3,}|(?:_[ \t]*){3,}|(?:-[ \t]*){3,})[ \t]*$/';
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
        $allEscapedChar = '/\\\\(' . self::REGEX_ESCAPABLE . ')/';

        $escaped = preg_replace($allEscapedChar, '$1', $string);
        $replaced = preg_replace_callback('/' . self::REGEX_ENTITY . '/i', function ($e) {
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
                $self = self::getInstance();

                return '/^(?:' . $self->getPartialRegex(self::OPENTAG) . '|' . $self->getPartialRegex(self::CLOSETAG) . ')\\s*$/i';
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
