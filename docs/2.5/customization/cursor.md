---
layout: default
title: Cursor
description: Using the Cursor object to parse Markdown content
---

# Cursor

A `Cursor` is essentially a fancy string wrapper that remembers your current position as you parse it.  It contains a set of highly-optimized methods making it easy to parse characters, match regular expressions, and more.

## Supported Encodings

As of now, only UTF-8 (and, by extension, ASCII) encoding is supported.

## Usage

Instantiating a new `Cursor` is as simple as:

```php
use League\CommonMark\Parser\Cursor;

$cursor = new Cursor('Hello World!');
```

Or, if you're creating a custom [block parser](/2.5/customization/block-parsing/) or [inline parser](/2.5/customization/inline-parsing/), a pre-configured `Cursor` will be provided to you with (with the `Cursor` already set to the current `position` trying to be parsed).

## Methods

You can then call any of the following methods to parse the string within that `Cursor`:

| Method                             | Purpose                                                                                                                         |
| ---------------------------------- | ------------------------------------------------------------------------------------------------------------------------------- |
| `getPosition()`                    | Returns the current position/index of the `Cursor` within the string                                                            |
| `getColumn()`                      | Returns the current column (used when handling tabbed indentation)                                                              |
| `getIndent()`                      | Returns the current amount of indentation                                                                                       |
| `isIndented()`                     | Returns whether the cursor is indented to `INDENT_LEVEL`                                                                        |
| `getCharacter(int $index)`         | Returns the character at the given absolute position                                                                            |
| `getCurrentCharacter()`            | Returns the character at the current position                                                                                   |
| `peek()`                           | Returns the next character without changing the current `position` of the cursor                                                |
| `peek(int $offset)`                | Returns the character `$offset` chars away without changing the current `position` of the cursor                                |
| `getNextNonSpacePosition()`        | Returns the position of the next character which is not a space or tab                                                          |
| `getNextNonSpaceCharacter()`       | Returns the next character which isn't a space (or tab)                                                                         |
| `advance()`                        | Moves the cursor forward by 1 character                                                                                         |
| `advanceBy(int $characters)`       | Moves the cursor forward by `$characters` characters                                                                            |
| `advanceBy(int $characters, true)` | Moves the cursor forward by `$characters` characters, handling tabs as columns                                                  |
| `advanceBySpaceOrTab()`            | Advances forward one character (and returns `true`) if it's a space or tab; returns false otherwise                             |
| `advanceToNextNonSpaceOrTab()`     | Advances forward past all spaces and tabs found, returning the number of such characters found                                  |
| `advanceToNextNonSpaceOrNewline()` | Advances forward past all spaces and newlines found, returning the number of such characters found                              |
| `advanceToEnd()`                   | Advances the position to the very end of the string, returning the number of such characters passed                             |
| `match(string $regex)`             | Attempts to match the given `$regex`; returns `null` if matching fails, otherwise it advances past and returns the matched text |
| `getPreviousText()`                | Returns the text that was just advanced through during the last `advance__()` or `match()` operation                            |
| `getRemainder()`                   | Returns the contents of the string from the current position through the end of the string                                      |
| `isBlank()`                        | Returns whether the remainder is blank (we're at the end or only space characters remain)                                       |
| `isAtEnd()`                        | Returns whether the cursor has reached the end of the string                                                                    |
| `saveState()`                      | Encapsulates the current state of the cursor into an `array` in case you need to `restoreState()` later                         |
| `restoreState($state)`             | Pass the result of `saveState()` back into here to restore the original state of the `Cursor`                                   |
| `getLine()`                        | Returns the entire string (not taking the position into account)                                                                |
