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

use League\CommonMark\Event\DocumentPreParsedEvent;
use League\CommonMark\Extension\FrontMatter\Yaml\FrontMatterParserInterface;
use League\CommonMark\Input\MarkdownInput;
use League\CommonMark\Parser\Cursor;

final class FrontMatterParserListener
{
    /**
     * @var FrontMatterParserInterface
     *
     * @psalm-readonly
     */
    private $frontMatterParser;

    private const REGEX_FRONT_MATTER = '/^---\\n.*\\n---\n/s';

    public function __construct(FrontMatterParserInterface $frontMatterParser)
    {
        $this->frontMatterParser = $frontMatterParser;
    }

    public function __invoke(DocumentPreParsedEvent $event): void
    {
        $content = $event->getMarkdown()->getContent();

        $cursor = new Cursor($content);

        // Locate the front matter
        $frontMatter = $cursor->match(self::REGEX_FRONT_MATTER);
        if ($frontMatter === null) {
            return;
        }

        // Trim the last 4 characters (ending ---s and newline)
        $frontMatter = \substr($frontMatter, 0, -4);

        // Parse the resulting YAML data
        $data = $this->frontMatterParser->parse($frontMatter);

        // Store the parsed data in the Document
        $event->getDocument()->data['front_matter'] = $data;

        // Advance through any remaining newlines which separated the front matter from the Markdown text
        $trailingNewlines = $cursor->match('/^\n+/');

        // Calculate how many lines the Markdown is offset from the front matter by counting the number of newlines
        // Don't forget to add 1 because we stripped one out when trimming the trailing delims
        $lineOffset = \preg_match_all('/\n/', $frontMatter . $trailingNewlines) + 1;

        $event->replaceMarkdown(new MarkdownInput($cursor->getRemainder(), $lineOffset));
    }
}
