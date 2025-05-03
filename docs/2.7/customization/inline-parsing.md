---
layout: default
title: Inline Parsing
description: Parsing inline elements with a custom parser
redirect_from: /customization/inline-parsing/
---

# Inline Parsing

There are two ways to implement custom inline syntax:

- Inline Parsers (covered here)
- [Delimiter Processors](/2.7/customization/delimiter-processing/)

The difference between normal inlines and delimiter-run-based inlines is subtle but important to understand.  In a nutshell, delimiter-run-based inlines:

- Are denoted by "wrapping" text with one or more characters before **and** after those inner contents
- Can contain other delimiter runs or inlines inside of them

An example of this would be emphasis:

```markdown
This is an example of **emphasis**. Note how the text is *wrapped* with the same character(s) before and after.
```

If your syntax looks like that, consider using a [delimiter processor](/2.7/customization/delimiter-processing/) instead.  Otherwise, an inline parser is your best bet.

## Implementing Inline Parsers

Inline parsers should implement `InlineParserInterface` and the following two methods:

### getMatchDefinition()

This method should return an instance of `InlineParserMatch` which defines the text the parser is looking for.  Examples of this might be something like:

```php
use League\CommonMark\Parser\Inline\InlineParserMatch;

InlineParserMatch::string('@');                  // Match any '@' characters found in the text
InlineParserMatch::string('foo');                // Match the text 'foo' (case insensitive)

InlineParserMatch::oneOf('@', '!');              // Match either character
InlineParserMatch::oneOf('http://', 'https://'); // Match either string

InlineParserMatch::regex('\d+');                 // Match the regular expression (omit the regex delimiters and any flags)
```

Once a match is found, the `parse()` method below may be called.

### parse()

This method will be called if both conditions are met:

1. The engine has found at a matching string in the current line; and,
2. No other inline parsers with a [higher priority](/2.7/customization/environment/#addinlineparser) have successfully parsed the text at this point in the line

#### Parameters

- `InlineParserContext $inlineContext` - Encapsulates the current state of the inline parser - see more information below.

##### InlineParserContext

This class has several useful methods:

- `getContainer()` - Returns the current container block the inline text was found in.  You'll almost always call `$inlineContext->getContainer()->appendChild(...)` to add the parsed inline text inside that block.
- `getReferenceMap()` - Returns the document's reference map
- `getCursor()` - Returns the current [`Cursor`](/2.7/customization/cursor/) used to parse the current line.  (Note that the cursor will be positioned **before** the matched text, so you must advance it yourself if you determine it's a valid match)
- `getDelimiterStack()` - Returns the current delimiter stack. Only used in advanced use cases.
- `getFullMatch()` - Returns the full string that matched you `InlineParserMatch` definition
- `getFullMatchLength()` - Returns the length of the full match - useful for advancing the cursor
- `getSubMatches()` - If your `InlineParserMatch` used a regular expression with capture groups, this will return the text matches by those groups.
- `getMatches()` - Returns an array where index `0` is the "full match", plus any sub-matches.  It basically simulates `preg_match()`'s behavior.

#### Return value

`parse()` should return `false` if it's unable to handle the text at the current position for any reason.  Other parsers will then have a chance to try parsing that text.  If all registered parsers return false, the text will be added as plain text.

Returning `true` tells the engine that you've successfully parsed the character (and related ones after it).  It is your responsibility to:

1. Advance the cursor to the end of the parsed/matched text
2. Add the parsed inline to the container (`$inlineContext->getContainer()->appendChild(...)`)

## Inline Parser Examples

### Example 1 - Twitter Handles

Let's say you wanted to autolink Twitter handles without using the link syntax.  This could be accomplished by registering a new inline parser to handle the `@` character:

```php
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;

class TwitterHandleParser implements InlineParserInterface
{
    public function getMatchDefinition(): InlineParserMatch
    {
        return InlineParserMatch::regex('@([A-Za-z0-9_]{1,15}(?!\w))');
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $cursor = $inlineContext->getCursor();
        // The @ symbol must not have any other characters immediately prior
        $previousChar = $cursor->peek(-1);
        if ($previousChar !== null && $previousChar !== ' ') {
            // peek() doesn't modify the cursor, so no need to restore state first
            return false;
        }

        // This seems to be a valid match
        // Advance the cursor to the end of the match
        $cursor->advanceBy($inlineContext->getFullMatchLength());

        // Grab the Twitter handle
        [$handle] = $inlineContext->getSubMatches();
        $profileUrl = 'https://twitter.com/' . $handle;
        $inlineContext->getContainer()->appendChild(new Link($profileUrl, '@' . $handle));
        return true;
    }
}

// And here's how to hook it up:

$environment = new Environment();
$environment->addExtension(new CommonMarkCoreExtension());
$environment->addInlineParser(new TwitterHandleParser());
```

### Example 2 - Emoticons

Let's say you want to automatically convert smilies (or "frownies") to emoticon images.  This is incredibly easy with an inline parser:

```php
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;

class SmilieParser implements InlineParserInterface
{
    public function getMatchDefinition(): InlineParserMatch
    {
        return InlineParserMatch::oneOf(':)', ':(');
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $cursor = $inlineContext->getCursor();

        // Advance the cursor past the 2 matched chars since we're able to parse them successfully
        $cursor->advanceBy(2);

        // Add the corresponding image
        if ($inlineContext->getFullMatch() === ':)') {
            $inlineContext->getContainer()->appendChild(new Image('/img/happy.png'));
        } elseif ($inlineContext->getFullMatch() === ':(') {
            $inlineContext->getContainer()->appendChild(new Image('/img/sad.png'));
        }

        return true;
    }
}

$environment = new Environment();
$environment->addExtension(new CommonMarkCoreExtension());
$environment->addInlineParser(new SmilieParserParser());
```

## Tips

- For best performance:
  - Avoid using overly-complex regular expressions in `getMatchDefinition()` - use the simplest regex you can and have `parse()` do the heavier validation
  - Have your `parse()` method `return false` **as soon as possible**.
- You can `peek()` without modifying the cursor state. This makes it useful for validating nearby characters as it's quick and you can bail without needed to restore state.
- You can look at (and modify) any part of the AST if needed (via `$inlineContext->getContainer()`).
