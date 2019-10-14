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

namespace League\CommonMark\Inline\Parser;

use League\CommonMark\Inline\Element\Code;
use League\CommonMark\Inline\Element\Text;
use League\CommonMark\InlineParserContext;
use function mb_substr;
use function preg_match;
use function preg_replace;
use function strlen;
use function substr;

final class BacktickParser implements InlineParserInterface
{
    /**
     * @return string[]
     */
    public function getCharacters(): array
    {
        return ['`'];
    }

    /**
     * @param InlineParserContext $inlineContext
     *
     * @return bool
     */
    public function parse(InlineParserContext $inlineContext): bool
    {
        $cursor = $inlineContext->getCursor();

        $ticks = $cursor->match('/^`+/');
        if ($ticks === '') {
            return false;
        }

        $currentPosition = $cursor->getPosition();
        $previousState = $cursor->saveState();

        while ($matchingTicks = $cursor->match('/`+/m')) {
            if ($matchingTicks === $ticks) {
                if ($cursor->isMultiByte()) {
                    $code = mb_substr(
                        $cursor->getLine(),
                        $currentPosition,
                        $cursor->getPosition() - $currentPosition - strlen($ticks),
                        $cursor->getEncoding()
                    );
                } else {
                    $code = substr(
                        $cursor->getLine(),
                        $currentPosition,
                        $cursor->getPosition() - $currentPosition - strlen($ticks)
                    );
                }

                $c = preg_replace('/\n/m', ' ', $code);

                if (
                    false === empty($c) &&
                    preg_match('/[^ ]/', $c) &&
                    strpos($c, ' ') === 0 &&
                    $c[strlen($c) - 1] === ' '
                ) {
                    $c = substr($c, 1, -1);
                }

                $inlineContext->getContainer()->appendChild(new Code($c));

                return true;
            }
        }

        // If we got here, we didn't match a closing backtick sequence
        $cursor->restoreState($previousState);
        $inlineContext->getContainer()->appendChild(new Text($ticks));

        return true;
    }
}
