<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\Mention;

use League\CommonMark\Extension\Mention\LinkGenerator\CallbackLinkGenerator;
use League\CommonMark\Extension\Mention\LinkGenerator\MentionLinkGeneratorInterface;
use League\CommonMark\Extension\Mention\LinkGenerator\StringTemplateLinkGenerator;
use League\CommonMark\Inline\Parser\InlineParserInterface;
use League\CommonMark\InlineParserContext;

final class MentionParser implements InlineParserInterface
{
    /** @var string */
    private $symbol;

    /** @var string */
    private $mentionRegex;

    /** @var MentionLinkGeneratorInterface */
    private $linkGenerator;

    public function __construct(string $symbol, string $mentionRegex, MentionLinkGeneratorInterface $linkGenerator)
    {
        $this->symbol = $symbol;
        $this->mentionRegex = $mentionRegex;
        $this->linkGenerator = $linkGenerator;
    }

    public function getCharacters(): array
    {
        return [$this->symbol];
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $cursor = $inlineContext->getCursor();

        // The symbol must not have any other characters immediately prior
        $previousChar = $cursor->peek(-1);
        if ($previousChar !== null && $previousChar !== ' ') {
            // peek() doesn't modify the cursor, so no need to restore state first
            return false;
        }

        // Save the cursor state in case we need to rewind and bail
        $previousState = $cursor->saveState();

        // Advance past the symbol to keep parsing simpler
        $cursor->advance();

        // Parse the handle
        $handle = $cursor->match($this->mentionRegex);
        if (empty($handle)) {
            // Regex failed to match; this isn't a valid mention
            $cursor->restoreState($previousState);

            return false;
        }

        $link = $this->linkGenerator->generateLink($this->symbol, $handle);

        if ($link === null) {
            $cursor->restoreState($previousState);

            return false;
        }

        $inlineContext->getContainer()->appendChild($link);

        return true;
    }

    public static function createWithStringTemplate(string $symbol, string $mentionRegex, string $urlTemplate): MentionParser
    {
        return new self($symbol, $mentionRegex, new StringTemplateLinkGenerator($urlTemplate));
    }

    public static function createWithCallback(string $symbol, string $mentionRegex, callable $callback): MentionParser
    {
        return new self($symbol, $mentionRegex, new CallbackLinkGenerator($callback));
    }

    public static function createTwitterHandleParser(): MentionParser
    {
        return self::createWithStringTemplate('@', '/^[A-Za-z0-9_]{1,15}(?!\w)/', 'https://twitter.com/%s');
    }

    public static function createGitHubHandleParser(): MentionParser
    {
        // RegEx adapted from https://github.com/shinnn/github-username-regex/blob/master/index.js
        return self::createWithStringTemplate('@', '/^[a-z\d](?:[a-z\d]|-(?=[a-z\d])){0,38}(?!\w)/', 'https://github.com/%s');
    }

    public static function createGitHubIssueParser(string $project): MentionParser
    {
        return self::createWithStringTemplate('#', '/^\d+/', "https://github.com/$project/issues/%d");
    }
}
