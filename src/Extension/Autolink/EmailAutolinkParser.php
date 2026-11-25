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

namespace League\CommonMark\Extension\Autolink;

use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;

final class EmailAutolinkParser implements InlineParserInterface
{
    // A local part can only start where a run of local-part characters starts, or where the previous
    // match ended (\G), so each run is scanned once rather than once per character
    private const REGEX = '(?:\G|(?<![A-Za-z0-9.\-_+]))[A-Za-z0-9.\-_+]++@[A-Za-z0-9\-_]++\.[A-Za-z0-9\-_.]++';

    public function getMatchDefinition(): InlineParserMatch
    {
        // Case-sensitive so that [A-Za-z] can't match non-ASCII letters such as the Kelvin sign, which
        // would make the byte length used by parse() differ from the character length
        return InlineParserMatch::regex(self::REGEX)->caseSensitive();
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $email = $inlineContext->getFullMatch();
        // The last character cannot be - or _
        if (\in_array(\substr($email, -1), ['-', '_'], true)) {
            return false;
        }

        // Does the URL end with punctuation that should be stripped?
        if (\substr($email, -1) === '.') {
            $email = \substr($email, 0, -1);
        }

        $inlineContext->getCursor()->advanceBy(\strlen($email));
        $inlineContext->getContainer()->appendChild(new Link('mailto:' . $email, $email));

        return true;
    }
}
