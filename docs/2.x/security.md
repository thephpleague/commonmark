---
layout: default
title: Security
description: How to configure league/commonmark against possible security issues when handling untrusted user input
redirect_from:
  - /security/
  - /2.0/security/
  - /2.1/security/
  - /2.2/security/
  - /2.3/security/
  - /2.4/security/
  - /2.5/security/
  - /2.6/security/
  - /2.7/security/
---

# Security

In order to be fully compliant with the CommonMark spec, certain security settings are disabled by default.  You will want to configure these settings if untrusted users will be providing the Markdown content:

- `html_input`: How to handle raw HTML
- `allow_unsafe_links`: Whether unsafe links are permitted
- `max_nesting_level`: Protect against long render times or segfaults
- `max_delimiters_per_line`: Protect against long parse times or rendering segfaults
- `xml/max_indentation_level`: Protect against oversized XML output (only relevant if you render XML)

Further information about each option can be found below.

## HTML Input

**All HTML input is unescaped by default.**  This behavior ensures that league/commonmark is 100% compliant with the CommonMark spec.

If you're developing an application which renders user-provided Markdown from potentially untrusted users, you are **strongly** encouraged to set the `html_input` option in your configuration to either `escape` or `strip`:

### Example - Escape all raw HTML input

```php
use League\CommonMark\CommonMarkConverter;

$converter = new CommonMarkConverter(['html_input' => 'escape']);
echo $converter->convert('<script>alert("Hello XSS!");</script>');

// &lt;script&gt;alert("Hello XSS!");&lt;/script&gt;
```

### Example - Strip all HTML from the input

```php
use League\CommonMark\CommonMarkConverter;

$converter = new CommonMarkConverter(['html_input' => 'strip']);
echo $converter->convert('<script>alert("Hello XSS!");</script>');

// (empty output)
```

**Failing to set this option could make your site vulnerable to cross-site scripting (XSS) attacks!**

See the [configuration](/2.x/configuration/) section for more information.

## Unsafe Links

Unsafe links are also allowed by default due to CommonMark spec compliance.  An unsafe link is one that uses any of these protocols:

- `javascript:`
- `vbscript:`
- `file:`
- `data:` (except for `data:image` in png, gif, jpeg, or webp format)

To prevent these from being parsed and rendered, you should set the `allow_unsafe_links` option to `false`.

## Nesting Level

**No maximum nesting level is enforced by default.**  Markdown content which is too deeply-nested (like 10,000 nested blockquotes: '> > > > > ...') [could result in long render times or segfaults](https://github.com/thephpleague/commonmark/issues/243#issuecomment-217580285).

When parsing untrusted input, set `max_nesting_level` to `100`.  Once this nesting level is hit, any subsequent Markdown will be rendered as plain text.  The limit can be lowered for stricter protection or raised explicitly for trusted documents which legitimately require deeper nesting.

### Example - Prevent deep nesting

```php
use League\CommonMark\CommonMarkConverter;

$markdown = str_repeat('> ', 10000) . ' Foo';

$converter = new CommonMarkConverter(['max_nesting_level' => 5]);
echo $converter->convert($markdown);

// <blockquote>
//   <blockquote>
//     <blockquote>
//       <blockquote>
//         <blockquote>
//           <p>&gt; &gt; &gt; &gt; &gt; &gt; &gt; ... Foo</p></blockquote>
//       </blockquote>
//     </blockquote>
//   </blockquote>
// </blockquote>
```

See the [configuration](/2.x/configuration/) section for more information.

## Max Delimiters Per Line

Similarly to the maximum nesting level, **no maximum number of delimiters per line is enforced by default.**  Delimiters can be nested (like `*a **b** c*`) or un-nested (like `*a* *b* *c*`) - in either case, having too many in a single line can result in long parse times. We therefore have a separate option to limit the number of delimiters per line.

If you need to parse untrusted input, consider setting a reasonable `max_delimiters_per_line` (perhaps 100-1000) depending on your needs.  Once this level is hit, any subsequent delimiters on that line will be rendered as plain text.  Note that this option only applies to emphasis-style delimiters; link and image brackets (`[` and `![`) are not limited by it, so consider also enforcing input size or line length limits upstream.

### Example - Prevent too many delimiters

```php
use League\CommonMark\CommonMarkConverter;

$markdown = '*a* **b *c **d** c* b**'; // 8 delimiters (* and **)

$converter = new CommonMarkConverter(['max_delimiters_per_line' => 6]);
echo $converter->convert($markdown);

// <p><em>a</em> **b *c <strong>d</strong> c* b**</p>
```

## XML Indentation Level

This only applies if you're [rendering XML](/2.x/xml/) instead of (or in addition to) HTML.

Pretty-printed XML indents each element by one level per step of nesting depth, so a document which is *n* levels deep emits roughly *n<sup>2</sup>* characters of whitespace.  A few kilobytes of deeply-nested Markdown could therefore be amplified into many megabytes of XML.

The `xml/max_indentation_level` option (default: `16`) caps how far elements are indented, which keeps the output size proportional to the size of the document.  Elements nested beyond that limit are still rendered in full - indentation is cosmetic, so the resulting XML remains well-formed and semantically identical.  Set the option to `0` for compact, unindented output.

Note that `max_nesting_level` is *not* sufficient on its own here, as it only constrains how deeply **blocks** may nest - deeply-nested inlines (like `_______...`) can still produce a deep tree.

## Table of Contents Placeholders

This only applies if you're using the [Table of Contents extension](/2.x/extensions/table-of-contents/) with `'position' => 'placeholder'`.

In placeholder mode every placeholder renders its own copy of the table of contents, so a document containing many headings *and* many placeholders repeats that whole list at each one.  A small input can therefore produce output many times its size.

The `table_of_contents/max_placeholder_entries` option bounds the total number of entries rendered across all of a document's placeholders; any placeholder beyond that budget is left as-is.  It defaults to `null` (no limit) for backward compatibility, so setting an `int` is recommended when rendering untrusted input.

## Additional Filtering

Although this library does offer these security features out-of-the-box, some users may opt to also run the HTML output through additional filtering layers (like HTMLPurifier).  If you do this, make sure you **thoroughly** test your additional post-processing steps and configure them to work properly with the types of HTML elements and attributes that converted Markdown might produce, otherwise, you may end up with weird behavior like missing images, broken links, mismatched HTML tags, etc.
