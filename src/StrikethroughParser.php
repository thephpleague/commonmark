<?php

/*
 * This file is part of the league/commonmark-ext-strikethrough package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com> and uAfrica.com (http://uafrica.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Ext\Strikethrough;

use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Inline\Parser\InlineParserInterface;
use League\CommonMark\InlineParserContext;

final class StrikethroughParser implements InlineParserInterface
{
    /**
     * {@inheritdoc}
     */
    public function getCharacters(): array
    {
        return ['~'];
    }

    /**
     * {@inheritdoc}
     */
    public function parse(InlineParserContext $inlineContext): bool
    {
        $cursor = $inlineContext->getCursor();
        $character = $cursor->getCharacter();
        if ($cursor->peek(1) !== $character) {
            return false;
        }

        $tildes = $cursor->match('/^~~+/');
        if ($tildes === '') {
            return false;
        }

        $previous_state = $cursor->saveState();
        while ($matching_tildes = $cursor->match('/~~+/m')) {
            if ($matching_tildes === $tildes) {
                $text = mb_substr($cursor->getPreviousText(), 0, -mb_strlen($tildes));
                $text = preg_replace('/[ \n]+/', ' ', $text);
                $inlineContext->getContainer()->appendChild(new Strikethrough(trim($text)));

                return true;
            }
        }

        // If we got here, we didn't match a closing tilde pair sequence
        $cursor->restoreState($previous_state);
        $inlineContext->getContainer()->appendChild(new Text($tildes));

        return true;
    }
}
