---
layout: default
title: Security
description: How to configure league/commonmark against possible security issues when handling untrusted user input
---

# Security

In order to be fully compliant with the CommonMark spec, certain security settings are disabled by default.  You will want to configure these settings if untrusted users will be providing the Markdown content:

- `html_input`: How to handle raw HTML
- `allow_unsafe_links`: Whether unsafe links are permitted
- `max_nesting_level`: Protected against long render times or segfaults

Further information about each option can be found below.

## HTML Input

**All HTML input is unescaped by default.**  This behavior ensures that league/commonmark is 100% compliant with the CommonMark spec.

If you're developing an application which renders user-provided Markdown from potentially untrusted users, you are **strongly** encouraged to set the `html_input` option in your configuration to either `escape` or `strip`:

### Example - Escape all raw HTML input

```php
use League\CommonMark\CommonMarkConverter;

$converter = new CommonMarkConverter(['html_input' => 'escape']);
echo $converter->convertToHtml('<script>alert("Hello XSS!");</script>');

// &lt;script&gt;alert("Hello XSS!");&lt;/script&gt;
```

### Example - Strip all HTML from the input

```php
use League\CommonMark\CommonMarkConverter;

$converter = new CommonMarkConverter(['html_input' => 'strip']);
echo $converter->convertToHtml('<script>alert("Hello XSS!");</script>');

// (empty output)
```

**Failing to set this option could make your site vulnerable to cross-site scripting (XSS) attacks!**

See the [configuration](/2.0/configuration/) section for more information.

## Unsafe Links

Unsafe links are also allowed by default due to CommonMark spec compliance.  An unsafe link is one that uses any of these protocols:

- `javascript:`
- `vbscript:`
- `file:`
- `data:` (except for `data:image` in png, gif, jpeg, or webp format)

To prevent these from being parsed and rendered, you should set the `allow_unsafe_links` option to `false`.

## Nesting Level

**No maximum nesting level is enforced by default.**  Markdown content which is too deeply-nested (like 10,000 nested blockquotes: '> > > > > ...') [could result in long render times or segfaults](https://github.com/thephpleague/commonmark/issues/243#issuecomment-217580285).

If you need to parse untrusted input, consider setting a reasonable `max_nesting_level` (perhaps 10-50) depending on your needs.  Once this nesting level is hit, any subsequent Markdown will be rendered as plain text.

### Example - Prevent deep nesting

```php
use League\CommonMark\CommonMarkConverter;

$markdown = str_repeat('> ', 10000) . ' Foo';

$converter = new CommonMarkConverter(['max_nesting_level' => 5]);
echo $converter->convertToHtml($markdown);

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

See the [configuration](/2.0/configuration/) section for more information.

## Additional Filtering

Although this library does offer these security features out-of-the-box, some users may opt to also run the HTML output through additional filtering layers (like HTMLPurifier).  If you do this, make sure you **thoroughly** test your additional post-processing steps and configure them to work properly with the types of HTML elements and attributes that converted Markdown might produce, otherwise, you may end up with weird behavior like missing images, broken links, mismatched HTML tags, etc.
