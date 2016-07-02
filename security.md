---
layout: default
title: Security
permalink: /security/
---

Security
========

**All HTML input is unescaped by default.**  This behavior ensures that league/commonmark is 100% compliant with the CommonMark spec.

If you're developing an application which renders user-provided Markdown from potentially untrusted users, you are **strongly** encouraged to set the `html_input` option in your configuration to either `escape` or `strip`:

## Example 1 - Escape all raw HTML input:

~~~php
use League\CommonMark\CommonMarkConverter;

$converter = new CommonMarkConverter(['html_input' => 'escape']);
echo $converter->convertToHtml('<script>alert("Hello XSS!");</script>');

// &lt;script&gt;alert("Hello XSS!");&lt;/script&gt;
~~~

## Example 2 - Strip all HTML from the input:
~~~php
use League\CommonMark\CommonMarkConverter;

$converter = new CommonMarkConverter(['html_input' => 'strip']);
echo $converter->convertToHtml('<script>alert("Hello XSS!");</script>');

// (empty output)
~~~

**Failing to set this option could make your site vulnerable to cross-site scripting (XSS) attacks!**

See the [configuration](/configuration/) section for more information.
