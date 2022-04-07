---
layout: default
title: Block Parsing
description: How to parse block-level elements
---

# Block Parsing

At a high level, block parsing is a two-step process:

 1. Using a `BlockStartParserInterface` to identify if/where a block start exists on the given line
 2. Using a `BlockContinueParserInterface` to perform additional processing of the identified block

So to implement a custom block parser you will actually need to implement both of these classes.

## `BlockStartParserInterface`

Instances of this interface have a single `tryStart()` method:

```php
/**
 * Check whether we should handle the block at the current position
 *
 * @param Cursor                       $cursor
 * @param MarkdownParserStateInterface $parserState
 *
 * @return BlockStart|null
 */
public function tryStart(Cursor $cursor, MarkdownParserStateInterface $parserState): ?BlockStart;
```

Given a [`Cursor`](/2.2/customization/cursor/) at the current position, plus some extra information about the state of the parser, this method is responsible for determining whether a particular type of block seems to exist at the given position.  You don't actually parse the block here - that's the job of a `BlockContinueParserInterface`.  Your only job here is to return whether or not a particular type of block does exist here, and if so which block parser should parse it.

If you find that you **cannot** parse the given block, you should `return BlockStart::none();` from this function.

However, if the Markdown at the current position does indeed seem to be the type of block you're looking for, you should return a `BlockStart` instance using the following static constructor pattern:

```php
use League\CommonMark\Parser\Block\BlockStart;

return BlockStart::of(new MyCustomParser())->at($cursor);
```

Unlike in 1.x, the `Cursor` state is no longer shared between parsers.  You must therefore explicitly provide the `BlockStart` object with a copy of your cursor at the correct, post-parsing position.

**NOTE:** If your custom block starts with a [letter character](http://unicode.org/reports/tr18/#General_Category_Property) you'll need to [add your parser to the environment](/2.2/customization/environment/#addblockstartparser) with a priority of `250` or higher.  This is due to a performance optimization where such lines are usually skipped.

## `BlockContinueParserInterface`

The previous interface only helps the engine identify where a block starts.  Additional information about the block, as well as the ability to parse additional lines of input, is all handled by the `BlockContinueParserInterface`.

This interface has several methods, so it's usually easier to extend from `AbstractBlockContinueParser` instead, which sets most of the methods to use typical defaults you can override as needed.

### `getBlock()`

```php
public function getBlock(): AbstractBlock;
```

Each instance of a `BlockContinueParserInterface` is associated with a new block that is being parsed.  This method here returns that block.

### `isContainer()`

```php
public function isContainer(): bool;
```

This method returns whether or not the block is a "container" capable of containing other blocks as children.

### `canContain()`

```php
public function canContain(AbstractBlock $childBlock): bool;
```

This method returns whether the current block being parsed can contain the given child block.

### `canHaveLazyContinuationLines()`

```php
public function canHaveLazyContinuationLines(): bool;
```

This method returns whether or not this parser should also receive subsequent lines of Markdown input.  This is primarily used when a block can span multiple lines, like code blocks do.

### `addLine()`

```php
public function addLine(string $line): void;
```

If `canHaveLazyContinuationLines()` returned `true`, this method will be called with the additional lines of content.

### `tryContinue()`

```php
public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser): ?BlockContinue;
```

This method allows you to try and parse an additional line of Markdown.

### `closeBlock()`

```php
public function closeBlock(): void;
```

This method is called when the block is done being parsed.  Any final adjustments to the block should be made at this time.

### `parseInlines()`

```php
public function parseInlines(InlineParserEngineInterface $inlineParser): void;
```

This method is called when the engine is ready to parse any inline child elements.

**Note:** For performance reasons, this method is not part of `BlockContinueParserInterface`. If your block may contain inlines, you should make sure that your "continue parser" also implements `BlockContinueParserWithInlinesInterface`.

## Tips

Here are some additional tips to consider when writing your own custom parsers:

### Combining both into one file

Although parsing requires two classes, you can use the anonymous class feature of PHP to combine both into a single file!  Here's an example:

```php
use League\CommonMark\Parser\Block\AbstractBlockContinueParser;
use League\CommonMark\Parser\Block\BlockStartParserInterface;

final class MyCustomBlockParser extends AbstractBlockContinueParser
{
    // TODO: implement your continuation parsing methods here

    public static function createBlockStartParser(): BlockStartParserInterface
    {
        return new class implements BlockStartParserInterface
        {
            // TODO: implement the tryStart() method here
        };
    }
}

```

### Performance

The `BlockStartParserInterface::tryStart()` and `BlockContinueParserInterface::tryContinue()` methods may be called hundreds or thousands of times during execution.  For best performance, have your methods return as early as possible, and make sure your code is highly optimized.

## Block Elements

In addition to creating a block parser, you may also want to have it return a custom "block element" - this is a class that extends from `AbstractBlock` and represents that particular block within the AST.

If your block contains literal strings/text within the block (and not as part of a child block), you should have your custom block type also `implement StringContainerInterface`.
