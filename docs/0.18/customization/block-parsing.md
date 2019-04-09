---
layout: default
title: Block Parsing
redirect_from: /customization/block-parsing/
---

Block Parsing
=============

Block parsers should extend from `AbstractBlockParser` and implement the following method:

## parse()

When parsing a new line, the `DocParser` iterates through all registered block parsers and calls their `parse()` method.  Each parser must determine whether it can handle the given line; if so, it should parse the given block and return true.

### Parameters

* `ContextInterface $context` - Provides information about the current context of the DocParser. Includes access to things like the document, current block container, and more.
* `Cursor $cursor` - The [`Cursor`](/0.18/customization/cursor/) Encapsulates the current state of the line being parsed and provides helpers for looking around the current position.

### Return value

`parse()` should return `false` if it's unable to handle the current line for any reason.  (The [`Cursor`](/0.18/customization/cursor/) state should be restored before returning false if modified). Other parsers will then have a chance to try parsing the line.  If all registered parsers return false, the line will be parsed as text.

Returning `true` tells the engine that you've successfully parsed the block at the given position.  It is your responsibility to:

1. Advance the cursor to the end of syntax indicating the block start
2. Add the parsed block via `$context->addBlock()`

## Tips

* For best performance, `return false` as soon as possible
