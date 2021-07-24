---
layout: default
title: Block Parsing
description: How to parse block-level elements
---

# Block Parsing

Block parsers should implement `BlockParserInterface` and implement the following method:

## parse()

```php
public function parse(ContextInterface $context, Cursor $cursor): bool;
```

When parsing a new line, the `DocParser` iterates through all registered block parsers and calls their `parse()` method.  Each parser must determine whether it can handle the given line; if so, it should parse the given block and return `true`.

### Parameters

- `ContextInterface $context` - Provides information about the current context of the DocParser. Includes access to things like the document, current block container, and more.
- `Cursor $cursor` - The [`Cursor`](/1.6/customization/cursor/) encapsulates the current state of the line being parsed and provides helpers for looking around the current position.

### Return value

`parse()` should return `false` if it's unable to handle the current line for any reason.  (The [`Cursor`](/1.6/customization/cursor/) state should be restored before returning false if modified). Other parsers will then have a chance to try parsing the line.  If all registered parsers return false, the line will be parsed as text.

Returning `true` tells the engine that you've successfully parsed the block at the given position.  It is your responsibility to:

1. Advance the cursor to the end of syntax indicating the block start
2. Add the parsed block via `$context->addBlock()`

## Tips

- For best performance, `return false` as soon as possible
- Your `parse()` method may be called thousands of times so be sure your code is optimized

## Block Elements

In addition to creating a block parser, you may also want to have it return a custom "block element" - this is a class that extends from `AbstractBlock` and represents that particular block within the AST.

Block elements also play a role during the parsing process as they tell the underlying engine how to handle subsequent blocks that are found.

### `AbstractBlockElement` Methods

| Method                    | Purpose                                                                                               |
| ------------------------- | ----------------------------------------------------------------------------------------------------- |
| `canContain(...)`         | Tell the engine whether a subsequent block can be added as a child of yours                           |
| `isCode()`                | Returns whether this block represents an extra-greedy `<code>` block                                  |
| `matchesNextLine(...)`    | Returns whether this block continues onto the next line (some blocks are multi-line)                  |
| `shouldLastLineBeBlank()` | Returns whether the last line should be blank (primarily used by `ListItem` elements)                 |
| `finalize(...)`           | Finalizes the block after all child items have been added, thus marking it as closed for modification |

For examples on how these methods are used, see the core block element classes included with this library.

### `AbstractStringContainerBlock`

If your element can contain strings of text, you should extend `AbstractStringContainerBlock` instead of `AbstractBlock`.  This provides some additional methods needed to manage that inner text:

| Method                         | Purpose                                                                                    |
| ------------------------------ | ------------------------------------------------------------------------------------------ |
| `handleRemainingContents(...)` | This is called when a block has been created but some other text still exists on that line |
| `addLine(...)`                 | Adds the given line of text to the block element                                           |
| `getStringContent()`           | Returns the strings contained with that block element                                      |

#### `InlineContainerInterface`

If the text contained by your block should be parsed for inline elements, you should also implement the `InlineContainerInterface`. This doesn't add any new methods but does signal to the engine that inline parsing is required.

### Multi-line Code Blocks

If you have a block which spans multiple lines and doesn't contain any child blocks, consider having `isCode()` return `true`.  Code blocks have a special feature which enables "greedy parsing" - once it first parses your block, the engine will assume that most of the subsequent lines of Markdown belong to your block - it won't try using any other parsers until your parser's `matchesNextLine()` method returns `false`, indicating that we've reached the end of that code block.
