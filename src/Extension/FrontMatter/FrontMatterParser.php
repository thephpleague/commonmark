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

namespace League\CommonMark\Extension\FrontMatter;

use League\CommonMark\Extension\FrontMatter\Data\FrontMatterDataParserInterface;
use League\CommonMark\Extension\FrontMatter\Exception\InvalidFrontMatterException;
use League\CommonMark\Extension\FrontMatter\Input\MarkdownInputWithFrontMatter;
use League\CommonMark\Parser\Cursor;

final class FrontMatterParser implements FrontMatterParserInterface
{
    /** @psalm-readonly */
    private FrontMatterDataParserInterface $frontMatterParser;

    public function __construct(FrontMatterDataParserInterface $frontMatterParser)
    {
        $this->frontMatterParser = $frontMatterParser;
    }

    /**
     * @throws InvalidFrontMatterException if the front matter cannot be parsed
     */
    public function parse(string $markdownContent): MarkdownInputWithFrontMatter
    {
        $cursor = new Cursor($markdownContent);

        // Locate the front matter
        $frontMatter = self::findFrontMatter($markdownContent);
        if ($frontMatter === null) {
            return new MarkdownInputWithFrontMatter($markdownContent);
        }

        $cursor->advanceBy(\mb_strlen($frontMatter, 'UTF-8'));

        // Trim the last line (ending ---s and newline)
        $frontMatter = \preg_replace('/---\R$/', '', $frontMatter);
        if ($frontMatter === null) {
            return new MarkdownInputWithFrontMatter($markdownContent);
        }

        // Parse the resulting YAML data
        $data = $this->frontMatterParser->parse($frontMatter);

        // Advance through any remaining newlines which separated the front matter from the Markdown text
        $trailingNewlines = $cursor->matchInPlace('/\G\R+/');

        // Calculate how many lines the Markdown is offset from the front matter by counting the number of newlines
        // Don't forget to add 1 because we stripped one out when trimming the trailing delims
        $lineOffset = \preg_match_all('/\R/', $frontMatter . $trailingNewlines) + 1;

        return new MarkdownInputWithFrontMatter($cursor->getRemainder(), $lineOffset, $data);
    }

    /**
     * Returns the leading `---` block, including its closing `---` line, or null if there isn't one
     *
     * Matches exactly what /\A---\R.*?\R---\R/s would, without the backtrack limit that regex hits at about 1MB
     */
    private static function findFrontMatter(string $content): ?string
    {
        $bodyStart = self::newlineLengthAt($content, 3);
        if ($bodyStart === 0 || \strncmp($content, '---', 3) !== 0) {
            return null;
        }

        $bodyStart += 3;
        if ($bodyStart >= \strlen($content)) {
            return null;
        }

        for ($pos = \strpos($content, '---', $bodyStart + 1); $pos !== false; $pos = \strpos($content, '---', $pos + 1)) {
            // The closing `---` must start a line, and that line break can't be the one ending the opening `---`
            if (self::newlineLengthAt($content, $pos - 1) === 0) {
                continue;
            }

            $closingNewline = self::newlineLengthAt($content, $pos + 3);
            if ($closingNewline > 0) {
                return \substr($content, 0, $pos + 3 + $closingNewline);
            }
        }

        return null;
    }

    /**
     * Returns how many bytes PCRE's \R would match at the given offset (0 if none)
     */
    private static function newlineLengthAt(string $content, int $offset): int
    {
        $char = $content[$offset] ?? '';
        if ($char === "\r") {
            return ($content[$offset + 1] ?? '') === "\n" ? 2 : 1;
        }

        return $char !== '' && \strpos("\n\x0b\x0c\x85", $char) !== false ? 1 : 0;
    }
}
