# Testing Guide

Multi-layered testing approach for quality, specification compliance, and security.

## Test Categories

- **Unit Tests** (`tests/unit/`) - Component testing, mirrors source structure
- **Functional Tests** (`tests/functional/`) - End-to-end with input `.md` and expected `.html` output pairs, includes `SpecTest.php`, `LocalDataTest.php`
- **Pathological Tests** (`tests/pathological/`) - Security/DoS prevention, CVE regression tests
- **Extension Tests** (`tests/functional/Extension/`) - Per-extension testing with multiple types

## Test Base Classes

- **AbstractLocalDataTestCase** - Data-driven testing with `.md`/`.html` pairs, supports YAML front matter
- **AbstractSpecTestCase** - CommonMark spec compliance testing

## Data-Driven Testing

- **File Pairs**: `.md` input + `.html` expected output
- **Spec Format**: CommonMark format with `example`/`.`/`expected` blocks
- **Front Matter**: YAML config (`html_input: escape`, `max_nesting_level: 100`)

## XML/AST Testing

- `*XmlTest.php` files validate AST structure with `.md`/`.xml` pairs

## Test Development

### Commands
- `composer test` - Full test suite
- `composer phpunit` - PHPUnit only
- `composer pathological` - Security/performance tests

### Adding Tests
- **Extensions**: `tests/functional/Extension/YourExtension/`
- **Unit**: Mirror source structure in `tests/unit/`
- **Data-driven**: Add `.md`/`.html` pairs

### File Naming
- `*Test.php` - Standard tests
- `*XmlTest.php` - AST validation
- `*SpecTest.php` - Spec compliance
- `*RegressionTest.php` - Regression prevention

## Security Testing

Pathological tests include CVE regression tests and DoS prevention (process isolation, timeouts, memory limits).

## Extension Testing Structure

```
tests/functional/Extension/YourExtension/
├── YourExtensionTest.php    # Main tests
├── YourExtensionXmlTest.php # AST validation
├── md/                      # Input files
├── html/                    # Expected output
└── xml/                     # Expected AST
```
