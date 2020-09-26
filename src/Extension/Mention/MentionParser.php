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

namespace League\CommonMark\Extension\Mention;

use League\CommonMark\Extension\Mention\Generator\CallbackGenerator;
use League\CommonMark\Extension\Mention\Generator\MentionGeneratorInterface;
use League\CommonMark\Extension\Mention\Generator\StringTemplateLinkGenerator;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;

final class MentionParser implements InlineParserInterface
{
    /**
     * @var string
     *
     * @psalm-readonly
     */
    private $prefix;

    /**
     * @var string
     *
     * @psalm-readonly
     */
    private $mentionRegex;

    /**
     * @var MentionGeneratorInterface
     *
     * @psalm-readonly
     */
    private $mentionGenerator;

    public function __construct(string $prefix, string $mentionRegex, MentionGeneratorInterface $mentionGenerator)
    {
        $this->prefix           = $prefix;
        $this->mentionRegex     = $mentionRegex;
        $this->mentionGenerator = $mentionGenerator;
    }

    public function getMatchDefinition(): InlineParserMatch
    {
        return InlineParserMatch::string($this->prefix);
    }

    public function parse(string $match, InlineParserContext $inlineContext): bool
    {
        $cursor = $inlineContext->getCursor();

        // The prefix must not have any other characters immediately prior
        $previousChar = $cursor->peek(-1);
        if ($previousChar !== null && $previousChar !== ' ') {
            // peek() doesn't modify the cursor, so no need to restore state first
            return false;
        }

        // Save the cursor state in case we need to rewind and bail
        $previousState = $cursor->saveState();

        // Advance past the prefix to keep parsing simpler
        $cursor->advanceBy(\mb_strlen($this->prefix));

        // Parse the identifier
        $identifier = $cursor->match($this->mentionRegex);
        if ($identifier === null) {
            // Regex failed to match; this isn't a valid mention
            $cursor->restoreState($previousState);

            return false;
        }

        $mention = $this->mentionGenerator->generateMention(new Mention($this->prefix, $identifier));

        if ($mention === null) {
            $cursor->restoreState($previousState);

            return false;
        }

        $inlineContext->getContainer()->appendChild($mention);

        return true;
    }

    public static function createWithStringTemplate(string $prefix, string $mentionRegex, string $urlTemplate): MentionParser
    {
        return new self($prefix, $mentionRegex, new StringTemplateLinkGenerator($urlTemplate));
    }

    public static function createWithCallback(string $prefix, string $mentionRegex, callable $callback): MentionParser
    {
        return new self($prefix, $mentionRegex, new CallbackGenerator($callback));
    }
}
