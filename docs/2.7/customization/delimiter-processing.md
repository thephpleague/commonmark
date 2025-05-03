---
layout: default
title: Delimiter Processing
description: Processing CommonMark delimiter runs with a custom processor
redirect_from: /customization/delimiter-processing/
---

# Delimiter Processing

Delimiter processors allow you to implement [delimiter runs](https://spec.commonmark.org/0.29/#delimiter-run) the same way the core library implements emphasis.

Delimiter runs are a special type of inline:

- They are denoted by "wrapping" text with one or more characters before **and** after those inner contents
- They can contain other delimiter runs or inlines inside of them

```markdown
This is an example of **emphasis**. Note how the text is *wrapped* with the same character(s) before and after.
```

When implementing something with these characteristics you should consider leveraging delimiter runs; otherwise, a basic [inline parser](/2.7/inline-parsing/) should be sufficient.

## Delimiter Priority

Delimiter processors have a lower priority than inline parsers - if an [inline parser](/2.7/inline-parsing/) successfully handles the same special character you're interested in then your delimiter processor will not be called.

## Implementing Standalone Delimiter Processors

Implement the `DelimiterProcessorInterface` and add it to your environment:

```php
$environment->addDelimiterProcessor(new MyCustomDelimiterProcessor());
```

### `getOpeningCharacter()` and `getClosingCharacter()`

These two methods tell the engine which characters are used to delineate your custom syntax.  Generally these will be the same, such as when using `*emphasis*`, but they can be different; for example, maybe you want to use `{this syntax}`.  Simply tell the engine which characters you'd like to use.

### `getMinimumLength()`

This method tells the engine the minimum number of characters needed to match or "activate" your processor. For example, if you want to match {% raw %}`{{example}}`{% endraw %} and not `{example}`, set this to `2`.

### `getDelimiterUse()`

```php
public function getDelimiterUse(DelimiterInterface $opener, DelimiterInterface $closer): int;
```

This method is used to tell the engine how many characters from the matching delimiters should be consumed.  For simple processors you'll likely return `1` (or whatever your minimum length is).  In more advanced cases, you can examine the opening and closing delimiters and perform additional logic to determine whether they should be fully or partially consumed.  You can also return `0` if you'd like.

### `process()`

```php
public function process(AbstractStringContainer $opener, AbstractStringContainer $closer, int $delimiterUse): void;
```

This is where the magic happens.  Once the engine determines it can use the delimiter it found (by looking at all the other methods above) it'll call this method.  Your job is to take everything between the `$opener` and `$closer` and wrap that in whatever custom inline element you'd like.  Here's a basic example of wrapping the inner contents inside a new `Emphasis` element:

```php
use League\CommonMark\Extension\CommonMark\Node\Inline\Emphasis;

// Create the outer element
$emphasis = new Emphasis();

// Add everything between $opener and $closer (exclusive) to the new outer element
$tmp = $opener->next();
while ($tmp !== null && $tmp !== $closer) {
    $next = $tmp->next();
    $emphasis->appendChild($tmp);
    $tmp = $next;
}

// Place the outer element into the AST
$opener->insertAfter($emphasis);
```

Note that `$opener` and `$closer` will be automatically removed for you after this function returns - no need to do that yourself.

## Combining Inline Parsers with Delimiter Processors

Basic delimiter processors, as covered above, do not require any custom inline parsers - they'll "just work".  But in some rare cases you may want to pair it with a custom [inline parser](/2.7/customization/inline-parsing/): the inline parser will identify the delimiter, adding an entry to the delimiter stack for the processor to process later.  Note that this is an advanced use case and you probably don't need this.  But if you do then read on.

### Inline Parsers and the Delimiter Stack

As your identifies potential delimiter-based inlines, it should create a new `AbstractStringContainer` node (either `Text` or something custom) with the inner contents and also push a new `DelimiterInterface` onto the `DelimiterStack`:

```php
use League\CommonMark\Delimiter\Delimiter;
use League\CommonMark\Node\Inline\Text;

$node = new Text($cursor->getPreviousText(), [
    'delim' => true,
]);
$inlineContext->getContainer()->appendChild($node);

// Add entry to stack to this opener
$delimiter = new Delimiter($character, $numDelims, $node, $canOpen, $canClose);
$inlineContext->getDelimiterStack()->push($delimiter);
```

This basically tells the engine that text was found which _might_ be emphasis, but due to the delimiter run rules we can't make that determination just yet.  That final determination is later on by a "delimiter processor".

Your implementation of the delimiter processor won't look any different in this approach - you'll still need to implement all of the same methods especially `process()`.  The difference is that **you've identified where the delimiter is, instead of relying on the engine to do this for you.**
