<?php

/*
 * This file is part of the commonmark-php package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on stmd.js
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ColinODell\CommonMark;

use ColinODell\CommonMark\Element\Delimiter;
use ColinODell\CommonMark\Element\InlineCreator;
use ColinODell\CommonMark\Element\InlineElementInterface;
use ColinODell\CommonMark\Reference\Reference;
use ColinODell\CommonMark\Reference\ReferenceMap;
use ColinODell\CommonMark\Util\ArrayCollection;
use ColinODell\CommonMark\Util\Html5Entities;
use ColinODell\CommonMark\Util\RegexHelper;
use ColinODell\CommonMark\Util\UrlEncoder;

/**
 * Parses inline elements
 */
class InlineParser
{
    /**
     * @var string
     */
    protected $subject;

    /**
     * @var int
     */
    protected $labelNestLevel = 0; // Used by parseLinkLabel method

    /**
     * @var int
     */
    protected $pos = 0;

    /**
     * @var ReferenceMap
     */
    protected $refmap;

    /**
     * @var RegexHelper
     */
    protected $regexHelper;

    /**
     * @var Delimiter|null
     */
    protected $delimiters;

    /**
     * Constrcutor
     */
    public function __construct()
    {
        $this->refmap = new ReferenceMap();
    }

    /**
     * If re matches at current position in the subject, advance
     * position in subject and return the match; otherwise return null
     * @param string $re
     *
     * @return string|null The match (if found); null otherwise
     */
    protected function match($re)
    {
        $matches = array();
        $subject = substr($this->subject, $this->pos);
        if (!preg_match($re, $subject, $matches, PREG_OFFSET_CAPTURE)) {
            return null;
        }

        // [0][0] contains the matched text
        // [0][1] contains the index of that match
        $this->pos += $matches[0][1] + strlen($matches[0][0]);

        return $matches[0][0];
    }

    /**
     * Returns the character at the current subject position, or null if
     * there are no more characters
     *
     * @return string|null
     */
    protected function peek()
    {
        $ch = substr($this->subject, $this->pos, 1);

        return false !== $ch && strlen($ch) > 0 ? $ch : null;
    }

    /**
     * Parse zero or more space characters, including at most one newline
     *
     * @return int
     */
    protected function spnl()
    {
        $this->match('/^ *(?:\n *)?/');

        return 1;
    }

    // All of the parsers below try to match something at the current position
    // in the subject.  If they succeed in matching anything, they
    // push an inline element onto the 'inlines' list.  They return the
    // number of characters parsed (possibly 0).

    /**
     * Attempt to parse backticks, adding either a backtick code span or a
     * literal sequence of backticks to the 'inlines' list.
     * @param \ColinODell\CommonMark\Util\ArrayCollection $inlines
     *
     * @return bool
     */
    protected function parseBackticks(ArrayCollection $inlines)
    {
        $ticks = $this->match('/^`+/');
        if (!$ticks) {
            return false;
        }

        $afterOpenTicks = $this->pos;
        $foundCode = false;
        $match = null;
        while (!$foundCode && ($match = $this->match('/`+/m'))) {
            if ($match == $ticks) {
                $c = substr($this->subject, $afterOpenTicks, $this->pos - $afterOpenTicks - strlen($ticks));
                $c = preg_replace('/[ \n]+/', ' ', $c);
                $inlines->add(InlineCreator::createCode(trim($c)));

                return true;
            }
        }

        // If we go here, we didn't match a closing backtick sequence
        $this->pos = $afterOpenTicks;
        $inlines->add(InlineCreator::createText($ticks));

        return true;
    }

    /**
     * Parse a backslash-escaped special character, adding either the escaped
     * character, a hard line break (if the backslash is followed by a newline),
     * or a literal backslash to the 'inlines' list.
     *
     * @param \ColinODell\CommonMark\Util\ArrayCollection $inlines
     *
     * @return bool
     */
    protected function parseBackslash(ArrayCollection $inlines)
    {
        $subject = $this->subject;
        $pos = $this->pos;
        if ($subject[$pos] !== '\\') {
            return false;
        }

        if (isset($subject[$pos + 1]) && $subject[$pos + 1] === "\n") {
            $this->pos += 2;
            $inlines->add(InlineCreator::createHardbreak());
        } elseif (isset($subject[$pos + 1]) &&
            preg_match('/' . RegexHelper::REGEX_ESCAPABLE . '/', $subject[$pos + 1])
        ) {
            $this->pos += 2;
            $inlines->add(InlineCreator::createText($subject[$pos + 1]));
        } else {
            $this->pos++;
            $inlines->add(InlineCreator::createText('\\'));
        }

        return true;
    }

    /**
     * Attempt to parse an autolink (URL or email in pointy brackets)
     * @param \ColinODell\CommonMark\Util\ArrayCollection $inlines
     *
     * @return bool
     */
    protected function parseAutolink(ArrayCollection $inlines)
    {
        $emailRegex = '/^<([a-zA-Z0-9.!#$%&\'*+\\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*)>/';
        $otherLinkRegex = '/^<(?:coap|doi|javascript|aaa|aaas|about|acap|cap|cid|crid|data|dav|dict|dns|file|ftp|geo|go|gopher|h323|http|https|iax|icap|im|imap|info|ipp|iris|iris.beep|iris.xpc|iris.xpcs|iris.lwz|ldap|mailto|mid|msrp|msrps|mtqp|mupdate|news|nfs|ni|nih|nntp|opaquelocktoken|pop|pres|rtsp|service|session|shttp|sieve|sip|sips|sms|snmp|soap.beep|soap.beeps|tag|tel|telnet|tftp|thismessage|tn3270|tip|tv|urn|vemmi|ws|wss|xcon|xcon-userid|xmlrpc.beep|xmlrpc.beeps|xmpp|z39.50r|z39.50s|adiumxtra|afp|afs|aim|apt|attachment|aw|beshare|bitcoin|bolo|callto|chrome|chrome-extension|com-eventbrite-attendee|content|cvs|dlna-playsingle|dlna-playcontainer|dtn|dvb|ed2k|facetime|feed|finger|fish|gg|git|gizmoproject|gtalk|hcp|icon|ipn|irc|irc6|ircs|itms|jar|jms|keyparc|lastfm|ldaps|magnet|maps|market|message|mms|ms-help|msnim|mumble|mvn|notes|oid|palm|paparazzi|platform|proxy|psyc|query|res|resource|rmi|rsync|rtmp|secondlife|sftp|sgn|skype|smb|soldat|spotify|ssh|steam|svn|teamspeak|things|udp|unreal|ut2004|ventrilo|view-source|webcal|wtai|wyciwyg|xfire|xri|ymsgr):[^<>\x00-\x20]*>/i';

        if ($m = $this->match($emailRegex)) {
            $email = substr($m, 1, -1);
            $inlines->add(InlineCreator::createLink('mailto:' . UrlEncoder::unescapeAndEncode($email), $email));

            return true;
        } elseif ($m = $this->match($otherLinkRegex)) {
            $dest = substr($m, 1, -1);
            $inlines->add(InlineCreator::createLink(UrlEncoder::unescapeAndEncode($dest), $dest));

            return true;
        } else {
            return false;
        }
    }

    /**
     * Attempt to parse a raw HTML tag
     * @param \ColinODell\CommonMark\Util\ArrayCollection $inlines
     *
     * @return bool
     */
    protected function parseHtmlTag(ArrayCollection $inlines)
    {
        if ($m = $this->match(RegexHelper::getInstance()->getHtmlTagRegex())) {
            $inlines->add(InlineCreator::createHtml($m));

            return true;
        }

        return false;
    }

    /**
     * Scan a sequence of characters == c, and return information about
     * the number of delimiters and whether they are positioned such that
     * they can open and/or close emphasis or strong emphasis.  A utility
     * function for strong/emph parsing.
     *
     * @param string $char
     *
     * @return array
     */
    protected function scanDelims($char)
    {
        $numDelims = 0;
        $startPos = $this->pos;

        $charBefore = $this->pos === 0 ? "\n" : $this->subject[$this->pos - 1];

        while ($this->peek() === $char) {
            $numDelims++;
            $this->pos++;
        }

        $charAfter = $this->peek() ? : "\n";

        $canOpen = $numDelims > 0 && !preg_match('/\s/', $charAfter);
        $canClose = $numDelims > 0 && !preg_match('/\s/', $charBefore);
        if ($char === '_') {
            $canOpen = $canOpen && !preg_match('/[a-z0-9]/i', $charBefore);
            $canClose = $canClose && !preg_match('/[a-z0-9]/i', $charAfter);
        }

        $this->pos = $startPos;

        return compact('numDelims', 'canOpen', 'canClose');
    }

    /**
     * @param string          $c
     * @param ArrayCollection $inlines
     *
     * @return bool
     */
    protected function parseEmphasis($c, ArrayCollection $inlines)
    {
        // Get opening delimiters
        $res = $this->scanDelims($c);
        $numDelims = $res['numDelims'];
        $startPos = $this->pos;

        if ($numDelims === 0) {
            return false;
        }

        $this->pos += $numDelims;
        $inlines->add(
            InlineCreator::createText(
                substr($this->subject, $startPos, $this->pos - $startPos)
            )
        );

        // Add entry to stack to this opener
        $this->delimiters = Delimiter::createNext($c, $numDelims, $inlines->count() - 1, $res['canOpen'], $res['canClose'], $this->delimiters);

        return true;
    }

    protected function removeDelimiter(Delimiter $delimiter)
    {
        if ($delimiter->getPrevious() !== null) {
            $delimiter->getPrevious()->setNext($delimiter->getNext());
        }

        if ($delimiter->getNext() === null) {
            // top of stack
            $this->delimiters = $delimiter->getPrevious();
        } else {
            $delimiter->getNext()->setPrevious($delimiter->getPrevious());
        }
    }

    protected function processEmphasis(ArrayCollection $inlines, Delimiter $stackBottom = null)
    {
        // Find first closer above stackBottom
        $closer = $this->delimiters;
        while ($closer !== null && $closer->getPrevious() !== $stackBottom) {
            $closer = $closer->getPrevious();
        }

        // Move forward, looking for closers, and handling each
        while ($closer !== null) {
            if ($closer->canClose() && (in_array($closer->getChar(), array('_', '*')))) {
                // Found emphasis closer. Now look back for first matching opener:
                $opener = $closer->getPrevious();
                while ($opener !== null && $opener !== $stackBottom) {
                    if ($opener->getChar() === $closer->getChar() && $opener->canOpen()) {
                        break;
                    }
                    $opener = $opener->getPrevious();
                }
                if ($opener !== null && $opener !== $stackBottom) {
                    // Calculate actual number of delimiters used from this closer
                    if ($closer->getNumDelims() < 3 || $opener->getNumDelims() < 3) {
                        $useDelims = $closer->getNumDelims() <= $opener->getNumDelims()
                            ? $closer->getNumDelims()
                            : $opener->getNumDelims();
                    } else {
                        $useDelims = $closer->getNumDelims() % 2 === 0 ? 2 : 1;
                    }

                    /** @var InlineElementInterface $openerInline */
                    $openerInline = $inlines->get($opener->getPos());
                    /** @var InlineElementInterface $closerInline */
                    $closerInline = $inlines->get($closer->getPos());

                    // Remove used delimiters from stack elts and inlines
                    $opener->setNumDelims($opener->getNumDelims() - $useDelims);
                    $closer->setNumDelims($closer->getNumDelims() - $useDelims);

                    $openerInline->setAttribute('c', substr($openerInline->getAttribute('c'), 0, -$useDelims));
                    $closerInline->setAttribute('c', substr($closerInline->getAttribute('c'), 0, -$useDelims));

                    // Build contents for new emph element
                    $start = $opener->getPos() + 1;
                    $contents = $inlines->slice($start, $closer->getPos() - $start);
                    $contents = array_filter($contents);

                    $emph = $useDelims === 1 ? InlineCreator::createEmph($contents) : InlineCreator::createStrong($contents);

                    // Insert into list of inlines
                    $inlines->set($opener->getPos() + 1, $emph);
                    for ($i = $opener->getPos() + 2; $i < $closer->getPos(); $i++) {
                        $inlines->set($i, null);
                    }

                    // Remove elts btw opener and closer in delimiters stack
                    $tempStack = $closer->getPrevious();
                    while ($tempStack !== null && $tempStack !== $opener) {
                        $nextStack = $tempStack->getPrevious();
                        $this->removeDelimiter($tempStack);
                        $tempStack = $nextStack;
                    }

                    // If opener has 0 delims, remove it and the inline
                    if ($opener->getNumDelims() === 0) {
                        $inlines->set($opener->getPos(), null);
                        $this->removeDelimiter($opener);
                    }

                    if ($closer->getNumDelims() === 0) {
                        $inlines->set($closer->getPos(), null);
                        $tempStack = $closer->getNext();
                        $this->removeDelimiter($closer);
                        $closer = $tempStack;
                    }
                } else {
                    $closer = $closer->getNext();
                }
            } else {
                $closer = $closer->getNext();
            }
        }

        // Remove gaps
        $inlines->removeGaps();

        // Remove all delimiters
        while ($this->delimiters && $this->delimiters !== $stackBottom) {
            $this->removeDelimiter($this->delimiters);
        }
    }

    /**
     * Attempt to parse link title (sans quotes)
     *
     * @return null|string The string, or null if no match
     */
    protected function parseLinkTitle()
    {
        if ($title = $this->match(RegexHelper::getInstance()->getLinkTitleRegex())) {
            // Chop off quotes from title and unescape
            return RegexHelper::unescape(substr($title, 1, strlen($title) - 2));
        } else {
            return null;
        }
    }

    /**
     * Attempt to parse link destination
     *
     * @return null|string The string, or null if no match
     */
    protected function parseLinkDestination()
    {
        if ($res = $this->match(RegexHelper::getInstance()->getLinkDestinationBracesRegex())) {
            // Chop off surrounding <..>:
            return UrlEncoder::unescapeAndEncode(
                RegexHelper::unescape(
                    substr($res, 1, strlen($res) - 2)
                )
            );
        } else {
            $res = $this->match(RegexHelper::getInstance()->getLinkDestinationRegex());
            if ($res !== null) {
                return UrlEncoder::unescapeAndEncode(
                    RegexHelper::unescape($res)
                );
            } else {
                return null;
            }
        }
    }

    /**
     * @return int
     */
    protected function parseLinkLabel()
    {
        $match = $this->match('/^\[(?:[^\\\\\[\]]|\\\\[\[\]]){0,750}\]/');

        return $match === null ? 0 : strlen($match);
    }

    /**
     * Add open bracket to delimiter stack and add a Text to inlines
     * @param ArrayCollection $inlines
     *
     * @return bool
     */
    protected function parseOpenBracket(ArrayCollection $inlines)
    {
        $startPos = $this->pos;
        $this->pos++;
        $inlines->add(InlineCreator::createText('['));

        // Add entry to stack for this opener
        $this->delimiters = Delimiter::createNext('[', 1, $inlines->count() - 1, true, false, $this->delimiters, $startPos);

        return true;
    }

    /**
     * If next character is [, and ! delimiter to delimiter stack and
     * add a Text to inlines. Otherwise just add a Text.
     *
     * @param ArrayCollection $inlines
     *
     * @return bool
     */
    protected function parseBang(ArrayCollection $inlines)
    {
        $startPos = $this->pos;
        $this->pos++;
        if ($this->peek() === '[') {
            $this->pos++;
            $inlines->add(InlineCreator::createText('!['));
            // Add entry to stack for this opener
            $this->delimiters = Delimiter::createNext('!', 1, $inlines->count() - 1, true, false, $this->delimiters, $startPos + 1);
        } else {
            $inlines->add(InlineCreator::createText('!'));
        }

        return true;
    }

    protected function parseCloseBracket(ArrayCollection $inlines)
    {
        $matched = false;
        $this->pos++;
        $startPos = $this->pos;

        // Look through stack of delimiters for a [ or !
        $opener = $this->delimiters;
        while ($opener !== null) {
            if ($opener->getChar() === '[' || $opener->getChar() === '!') {
                break;
            }
            $opener = $opener->getPrevious();
        }

        if ($opener === null) {
            // No matched opener, just return a literal
            $inlines->add(InlineCreator::createText(']'));

            return true;
        }

        // If we got here, open is a potential opener
        $isImage = $opener->getChar() === '!';
        // Instead of copying a slice, we null out the
        // parts of inlines that don't correspond to linkText;
        // later, we'll collapse them. This is awkways, and coul
        // be simplified if we made inlines a linked list rather than
        // and array:
        $linkText = $inlines->slice(0);
        for ($i = 0; $i < $opener->getPos() + 1; $i++) {
            $linkText[$i] = null;
        }
        $linkText = new ArrayCollection($linkText);

        // Check to see if we have a link/image

        // Inline link?
        if ($this->peek() == '(') {
            $this->pos++;
            if ($this->spnl() &&
                (($dest = $this->parseLinkDestination()) !== null) &&
                $this->spnl()
            ) {
                // make sure there's a space before the title:
                if (preg_match('/^\\s/', $this->subject[$this->pos - 1])) {
                    $title = $this->parseLinkTitle() ?: '';
                } else {
                    $title = null;
                }

                if ($this->spnl() && $this->match('/^\\)/')) {
                    $matched = true;
                }
            }
        } else {
            // Next, see if there's a link label
            $savePos = $this->pos;
            $this->spnl();
            $beforeLabel = $this->pos;
            $n = $this->parseLinkLabel();
            if ($n === 0 || $n === 2) {
                // Empty or missing second label
                $reflabel = substr($this->subject, $opener->getIndex(), $startPos - $opener->getIndex());
            } else {
                $reflabel = substr($this->subject, $beforeLabel, $n);
            }

            if ($n === 0) {
                // If shortcut reference link, rewind before spaces we skipped
                $this->pos = $savePos;
            }

            // Lookup rawlabel in refmap
            if ($link = $this->refmap->getReference($reflabel)) {
                $dest = $link->getDestination();
                $title = $link->getTitle();
                $matched = true;
            }
        }

        if ($matched) {
            $this->processEmphasis($linkText, $opener->getPrevious());

            // Remove the part of inlines that become link_text
            // See noter above on why we need to do this instead of splice:
            for ($i = $opener->getPos(); $i < $inlines->count(); $i++) {
                $inlines->set($i, null);
            }

            // processEmphasis will remove this and later delimiters.
            // Now, for a link, we also remove earlier link openers
            // (no links in links)
            if (!$isImage) {
                $opener = $this->delimiters;
                $closerAbove = null;
                while ($opener !== null) {
                    if ($opener->getChar() === '[') {
                        if ($closerAbove) {
                            $closerAbove->setPrevious($opener->getPrevious());
                        } else {
                            $this->delimiters = $opener->getPrevious();
                        }
                    } else {
                        $closerAbove = $opener;
                    }
                    $opener = $opener->getPrevious();
                }
            }

            if ($isImage) {
                $inlines->add(InlineCreator::createImage($dest, $linkText, $title));
            } else {
                $inlines->add(InlineCreator::createLink($dest, $linkText, $title));
            }

            return true;
        } else { // No match
            $this->removeDelimiter($opener); // Remove this opener from stack
            $this->pos = $startPos;
            $inlines->add(InlineCreator::createText(']'));

            return true;
        }
    }

    /**
     * Attempt to parse an entity, adding to inlines if successful
     * @param \ColinODell\CommonMark\Util\ArrayCollection $inlines
     *
     * @return bool
     */
    protected function parseEntity(ArrayCollection $inlines)
    {
        if ($m = $this->match(RegexHelper::REGEX_ENTITY)) {
            $inlines->add(InlineCreator::createText(Html5Entities::decodeEntity($m)));

            return true;
        }

        return false;
    }

    /**
     * Parse a run of ordinary characters, or a single character with
     * a special meaning in markdown, as a plain string, adding to inlines.
     *
     * @param \ColinODell\CommonMark\Util\ArrayCollection $inlines
     *
     * @return bool
     */
    protected function parseString(ArrayCollection $inlines)
    {
        if ($m = $this->match(RegexHelper::getInstance()->getMainRegex())) {
            $inlines->add(InlineCreator::createText($m));

            return true;
        }

        return false;
    }

    /**
     * Parse a newline.
     *
     * @param \ColinODell\CommonMark\Util\ArrayCollection $inlines
     *
     * @return bool
     */
    protected function parseNewline(ArrayCollection $inlines)
    {
        if ($m = $this->match('/^ *\n/')) {
            if (strlen($m) > 2) {
                $inlines->add(InlineCreator::createHardbreak());
            } elseif (strlen($m) > 0) {
                $inlines->add(InlineCreator::createSoftbreak());
            }

            return true;
        }

        return false;
    }

    /**
     * Parse the next inline element in subject, advancing subject position
     * and adding the result to 'inlines'.
     *
     * @param \ColinODell\CommonMark\Util\ArrayCollection $inlines
     *
     * @return bool
     */
    protected function parseInline(ArrayCollection $inlines)
    {
        $c = $this->peek();
        if ($c === null) {
            return false;
        }

        switch ($c) {
            case "\n":
            case ' ':
                $res = $this->parseNewline($inlines);
                break;
            case '\\':
                $res = $this->parseBackslash($inlines);
                break;
            case '`':
                $res = $this->parseBackticks($inlines);
                break;
            case '*':
            case '_':
                $res = $this->parseEmphasis($c, $inlines);
                break;
            case '[':
                $res = $this->parseOpenBracket($inlines);
                break;
            case '!':
                $res = $this->parseBang($inlines);
                break;
            case ']':
                $res = $this->parseCloseBracket($inlines);
                break;
            case '<':
                $res = $this->parseAutolink($inlines) ? : $this->parseHtmlTag($inlines);
                break;
            case '&':
                $res = $this->parseEntity($inlines);
                break;
            default:
                $res = $this->parseString($inlines);
        }

        if (!$res) {
            $this->pos++;
            $inlines->add(InlineCreator::createText($c));
        }

        return true;
    }

    /**
     * Parse s as a list of inlines, using refmap to resolve references.
     *
     * @param string $s
     * @param ReferenceMap $refMap
     *
     * @return ArrayCollection|InlineElementInterface[]
     */
    protected function parseInlines($s, ReferenceMap $refMap)
    {
        $this->subject = $s;
        $this->pos = 0;
        $this->refmap = $refMap;
        $this->delimiters = null;
        $inlines = new ArrayCollection();
        while ($this->parseInline($inlines)) {
            ;
        }
        $this->processEmphasis($inlines, null);

        return $inlines;
    }

    /**
     * @param string       $s
     * @param ReferenceMap $refMap
     *
     * @return ArrayCollection|Element\InlineElementInterface[]
     */
    public function parse($s, ReferenceMap $refMap)
    {
        return $this->parseInlines($s, $refMap);
    }

    /**
     * Attempt to parse a link reference, modifying refmap.
     * @param string       $s
     * @param ReferenceMap $refMap
     *
     * @return int
     */
    public function parseReference($s, ReferenceMap $refMap)
    {
        $this->subject = $s;
        $this->pos = 0;
        $startPos = $this->pos;

        // label
        $matchChars = $this->parseLinkLabel();
        if ($matchChars === 0) {
            return 0;
        } else {
            $label = substr($this->subject, 0, $matchChars);
        }

        // colon
        if ($this->peek() === ':') {
            $this->pos++;
        } else {
            $this->pos = $startPos;

            return 0;
        }

        // link url
        $this->spnl();

        $destination = $this->parseLinkDestination();
        if ($destination === null || strlen($destination) === 0) {
            $this->pos = $startPos;

            return 0;
        }

        $beforeTitle = $this->pos;
        $this->spnl();
        $title = $this->parseLinkTitle();
        if ($title === null) {
            $title = '';
            // rewind before spaces
            $this->pos = $beforeTitle;
        }

        // make sure we're at line end:
        if ($this->match('/^ *(?:\n|$)/') === null) {
            $this->pos = $startPos;

            return 0;
        }

        if (!$refMap->contains($label)) {
            $refMap->addReference(new Reference($label, $destination, $title));
        }

        return $this->pos - $startPos;
    }
}
