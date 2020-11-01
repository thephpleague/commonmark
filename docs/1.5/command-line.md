---
layout: default
title: Command Line
redirect_from: /command-line/
---

# Command Line

**This functionality has been deprecated in version 1.4 and will be removed in 2.0.**

Markdown can be converted at the command line using the `./bin/commonmark` script.

## Usage

```bash
./bin/commonmark [OPTIONS] [FILE]
```

- `-h`, `--help`: Shows help and usage information
- `--enable-em`: Disable `<em>` parsing by setting to `0`; enable with `1` (default: `1`)
- `--enable-strong`: Disable `<strong>` parsing by setting to `0`; enable with `1` (default: `1`)
- `--use-asterisk`: Disable parsing of `*` for emphasis by setting to `0`; enable with `1` (default: `1`)
- `--use-underscore`: Disable parsing of `_` for emphasis by setting to `0`; enable with `1` (default: `1`)

If no file is given, input will be read from STDIN.

Output will be written to STDOUT.

## Examples

### Converting a file named document.md

```bash
./bin/commonmark document.md
```

### Converting a file and saving its output

```bash
./bin/commonmark document.md > output.html
```

### Converting from STDIN

```bash
echo -e '# Hello World!' | ./bin/commonmark
```

### Converting from STDIN and saving the output

```bash
echo -e '# Hello World!' | ./bin/commonmark > output.html
```
