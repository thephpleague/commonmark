---
layout: default
title: Inline Parsing
permalink: /customization/inline-parsing/
---

Inline Parsing
==============

Inline parsers should extend from `AbstractInlineParser` and implement two methods:

## getCharacters()

This method should return an array of single characters which the inline parser engine should stop on.  When it does find a match in the current line the `parse()` method below may be called.

## parse()

This method will be called if both conditions are met:

1. The engine has stopped at a matching character; and,
2. No other inline parsers have successfully parsed the character

### Parameters

* `ContextInterface $context` - Provides information about the current context of the DocParser. Includes access to things like the document, current block container, and more.
* `InlineParserContext $inlineContext` - Encapsulates the current state of the inline parser, including the `Cursor` used to parse the current line.

### Return value

`parse()` should return `false` if it's unable to handle the current line/character for any reason.  (The `Cursor` state should be restored before returning false if modified). Other parsers will then have a chance to try parsing the line.  If all registered parsers return false, the character will be added as plain text.

Returning `true` tells the engine that you've successfully parsed the character (and related ones after it).  It is your responsibility to:

1. Advance the cursor to the end of the parsed text
2. Add the parsed inline to the `$inlineContext->getInlines()` collection

## Example

Let's say you wanted to autolink Twitter handles without using the link syntax.  This could be accomplished by registering a new inline parser to handle the `@` character:

~~~php
class TwitterHandleParser extends AbstractInlineParser
{
    public function getCharacters() {
        return array('@');
    }

    public function parse(ContextInterface $context, InlineParserContext $inlineContext) {
        $cursor = $inlineContext->getCursor();

        // The @ symbol must not have any other characters immediately prior
        $previousChar = $cursor->peek(-1);
        if ($previousChar !== null && $previousChar !== ' ') {
            // peek() doesn't modify the cursor, so no need to restore state first
            return false;
        }

        // Save the cursor state in case we need to rewind and bail
        $previousState = $cursor->saveState();

        // Advance past the @ symbol to keep parsing simpler
        $cursor->advance();

        // Parse the handle
        $handle = $cursor->match('/^\w+/');
        if (empty($handle)) {
            // Regex failed to match; this isn't a valid Twitter handle
            $cursor->restoreState($previousState);

            return false;
        }

        $profileUrl = 'https://twitter.com/' . $handle;

        $inlineContext->getInlines()->add(new Link($profileUrl, '@'.$handle));

        return true;
    }
}

$environment = Environment::createCommonMarkEnvironment();
$environment->addInlineParser(new TwitterHandleParser());
~~~

## Tips

* For best performance, `return false` as soon as possible
* You can `peek()` without modifying the cursor state. This makes it useful for validating nearby characters as it's quick and you can bail without needed to restore state.
* You can look at (and modify) previously-parsed inlines if needed.
