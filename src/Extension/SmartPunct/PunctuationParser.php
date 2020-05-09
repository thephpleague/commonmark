<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\SmartPunct;

use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\InlineParserContext;

final class PunctuationParser implements InlineParserInterface
{
    /**
     * {@inheritdoc}
     */
    public function getCharacters(): array
    {
        return ['-', '.'];
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $cursor = $inlineContext->getCursor();
        $ch     = $cursor->getCharacter();

        // Ellipses
        if ($ch === '.' && $matched = $cursor->match('/^\\.( ?\\.)\\1/')) {
            $inlineContext->getContainer()->appendChild(new Text('…'));

            return true;
        }

        // Em/En-dashes
        if ($ch === '-' && $matched = $cursor->match('/^(?<!-)(-{2,})/')) {
            $count   = \strlen($matched);
            $enDash  = '–';
            $enCount = 0;
            $emDash  = '—';
            $emCount = 0;
            if ($count % 3 === 0) { // If divisible by 3, use all em dashes
                $emCount = (int) ($count / 3);
            } elseif ($count % 2 === 0) { // If divisible by 2, use all en dashes
                $enCount = (int) ($count / 2);
            } elseif ($count % 3 === 2) { // If 2 extra dashes, use en dash for last 2; em dashes for rest
                $emCount = (int) (($count - 2) / 3);
                $enCount = 1;
            } else { // Use en dashes for last 4 hyphens; em dashes for rest
                $emCount = (int) (($count - 4) / 3);
                $enCount = 2;
            }

            $inlineContext->getContainer()->appendChild(new Text(
                \str_repeat($emDash, $emCount) . \str_repeat($enDash, $enCount)
            ));

            return true;
        }

        return false;
    }
}
