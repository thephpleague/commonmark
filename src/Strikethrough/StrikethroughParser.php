<?php
namespace CommonMarkExt\Strikethrough;

use League\CommonMark\ContextInterface;
use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Inline\Parser\AbstractInlineParser;
use League\CommonMark\InlineParserContext;

class StrikethroughParser extends AbstractInlineParser
{
    /**
     * @return string[]
     */
    public function getCharacters()
    {
        return ['~'];
    }

    /**
     * @param ContextInterface $context
     * @param InlineParserContext $inline_context
     *
     * @return bool
     */
    public function parse(InlineParserContext $inline_context)
    {
        $cursor = $inline_context->getCursor();
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
                $text = mb_substr( $cursor->getPreviousText(), 0, -mb_strlen($tildes) );
                $text = preg_replace('/[ \n]+/', ' ', $text);
                $inline_context->getContainer()->appendChild(new Strikethrough(trim($text)));
                return true;
            }
        }
        // If we got here, we didn't match a closing tilde pair sequence
        $cursor->restoreState($previous_state);
        $inline_context->getContainer()->appendChild(new Text($tildes));
        return true;
    }
}
