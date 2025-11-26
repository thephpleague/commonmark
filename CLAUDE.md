# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

league/commonmark is a highly-extensible PHP Markdown parser that fully supports the CommonMark spec and GitHub-Flavored Markdown (GFM). It's based on the CommonMark JS reference implementation and provides a robust, extensible architecture for parsing and rendering Markdown content.

## Development Commands

### Testing
- `composer test` - Run all tests (includes linting, static analysis, unit tests, and pathological tests)
- `composer phpunit` - Run PHPUnit tests only (no coverage)
- `composer pathological` - Run pathological performance tests

### Code Quality
- `composer phpcs` - Run PHP CodeSniffer for coding standards
- `composer phpcbf` - Automatically fix coding standards issues
- `composer phpstan` - Run PHPStan static analysis
- `composer psalm` - Run Psalm static analysis with stats

(IMPORTANT: you MUST ALWAYS use PHP 7.4 to run `phpcs` and `phpcbf`. You SHOULD use the `php` service from docker-compose, which uses that version. Example: `docker compose exec php composer phpcs`)

### Benchmarking
- `./tests/benchmark/benchmark.php` - Compare performance against other Markdown parsers

## Architecture Overview

### Core Components

**Converters**: Main entry points using Facade pattern
- `CommonMarkConverter` - Preconfigured with `CommonMarkCoreExtension`
- `GithubFlavoredMarkdownConverter` - Includes GFM extensions bundle
- `MarkdownConverter` - Base class orchestrating `MarkdownParser` + `HtmlRenderer`
- Pattern: Factory with default configurations + Facade for complex pipeline

**Environment System**: Service container and registry
- `Environment` - Central registry managing parsers/renderers with priorities
- Implements PSR-14 event dispatcher for pre/post processing hooks
- Uses lazy initialization - extensions registered on first use
- Pattern: Registry + Builder + Dependency Injection

**Parser Architecture**: Two-phase recursive descent parsing
- **Block Phase**: `MarkdownParser` processes line-by-line with active parser stack
  - `BlockStartParserInterface` - Strategy pattern for block detection
  - State machine with continuation tracking and reference processing
  - Security: NUL character replacement, configurable nesting limits
- **Inline Phase**: `InlineParserEngine` with regex pre-compilation
  - `InlineParserInterface` - Strategy with regex-based matching
  - Position-based parser coordination with delimiter processing
  - Adjacent text merging optimization

**AST (Abstract Syntax Tree)**: Composite pattern with doubly-linked structure
- `Node` base class with tree navigation/manipulation methods
- `AbstractBlock`/`AbstractInline` - Template method pattern for element types
- `Document` - Root node with reference map storage
- Uses `Dflydev\DotAccessData\Data` for flexible metadata storage
- Supports multiple traversal: iterator, walker, query system

**Rendering**: Visitor pattern with strategy delegation
- `HtmlRenderer` - Traverses AST, delegates to node-specific renderers
- `NodeRendererInterface` - Strategy pattern for extensible rendering
- Hierarchical renderer lookup supporting inheritance
- Pre/post-render events with configurable block separators

**Extension System**: Plugin pattern with composite support
- `ExtensionInterface` - Simple contract for environment configuration
- `CommonMarkCoreExtension` - Complete spec implementation with priorities
- `GithubFlavoredMarkdownExtension` - Composite bundling multiple GFM features
- Performance: Optimized parser ordering and lazy registration

### Key Directories

**`src/Extension/`**: All built-in extensions
- `CommonMark/` - Core CommonMark specification features
- `GithubFlavoredMarkdownExtension.php` - GFM bundle extension
- Individual feature extensions: `Table/`, `Strikethrough/`, `TaskList/`, etc.

**`src/Parser/`**: Parsing logic
- `Block/` - Block-level parsing components
- `Inline/` - Inline parsing components
- `MarkdownParser.php` - Main parsing coordinator

**`src/Node/`**: AST node definitions
- `Block/` - Block-level nodes (paragraphs, headings, lists, etc.)
- `Inline/` - Inline nodes (text, emphasis, links, etc.)

**`src/Renderer/`**: Output rendering
- `Block/` and `Inline/` subdirectories mirror node structure
- `HtmlRenderer.php` - Main HTML output renderer

## AST (Abstract Syntax Tree) Manipulation

The library uses a doubly-linked AST where all elements (including the root `Document`) extend from the `Node` class:

### AST Traversal Methods

- **Iterator**: `$node->iterator()` - Fastest for complete tree traversal
- **Walker**: `$node->walker()` - Full control with enter/leave events, use `resumeAt()` for safe modifications
- **Query**: `(new Query())->where()->findAll($node)` - Easy but memory-intensive, creates snapshots
- **Manual**: `$node->next()`, `$node->parent()`, `$node->children()` - Best for direct relationships

### AST Modification

- **Adding**: `appendChild()`, `prependChild()`, `insertAfter()`, `insertBefore()`
- **Removing**: `detach()`, `replaceWith()`, `detachChildren()`, `replaceChildren()`
- **Data**: `$node->data->set('custom/info', $value)`, `$node->data->set('attributes/class', 'css-class')`

## Extension Development

### Creating Extensions
1. Implement `ExtensionInterface` with `register(EnvironmentBuilderInterface $environment)` method
2. Register components with priorities: `addInlineParser()`, `addBlockStartParser()`, `addRenderer()`
3. Follow existing extension patterns in `src/Extension/`

### Key Interfaces
- **Block Parsers**: `BlockStartParserInterface` - implement `tryStart()` and `tryContinue()`
- **Inline Parsers**: `InlineParserInterface` - implement `getMatchDefinition()` and `parse()`
- **Delimiter Processors**: `DelimiterProcessorInterface` - for emphasis-style wrapping syntax
- **Renderers**: `NodeRendererInterface` - implement `render()`, use `HtmlElement` for safety
- **Events**: PSR-14 events like `DocumentParsedEvent` for AST manipulation
- **Configuration**: `ConfigurableExtensionInterface` with `league/config` validation

### Cursor Usage & Parsing
- `Cursor` class: dual ASCII/UTF-8 paths, character caching, position state management
- Key methods: `peek()`, `match()`, `saveState()`/`restoreState()`, `advanceBy()`

## Testing Strategy

### Test Categories & Commands
- **Unit Tests** (`tests/unit/`) - Component testing, mirrors source structure
- **Functional Tests** (`tests/functional/`) - End-to-end with `.md`/`.html` pairs
- **Pathological Tests** (`tests/pathological/`) - Security/DoS prevention
- **Extension Tests** (`tests/functional/Extension/`) - Per-extension testing

### Running Tests
- `composer test` - Full test suite
- `composer phpunit` - PHPUnit tests only
- `composer pathological` - Security/performance tests

## Security Configuration (CRITICAL for Untrusted Input)

When handling untrusted user input, certain security settings are essential to prevent XSS, DoS, and other attacks. These particular ones should be checked where necessary:

### HTML Input Security (`html_input`)

**Implementation**: `HtmlFilter::filter()` in `HtmlBlockRenderer` and `HtmlInlineRenderer`
**Default**: `'allow'` (unsafe for untrusted input)
**Attack Vector**: XSS through raw HTML injection

**Options**:
- `HtmlFilter::STRIP` returns empty string
- `HtmlFilter::ESCAPE` uses `htmlspecialchars($html, ENT_NOQUOTES)`
- `HtmlFilter::ALLOW` returns raw HTML unchanged

### Unsafe Links Protection (`allow_unsafe_links`)

**Implementation**: `RegexHelper::isLinkPotentiallyUnsafe()` in `LinkRenderer` and `ImageRenderer`
**Default**: `true` (allows unsafe links)
**Attack Vector**: XSS through malicious protocols (javascript:, vbscript:, file:, data:)
