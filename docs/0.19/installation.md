---
layout: default
title: Installation
---

# Installation

The recommended installation method is via Composer.

In your project root just run:

~~~bash
composer require league/commonmark:^0.19
~~~

Ensure that you’ve set up your project to [autoload Composer-installed packages](https://getcomposer.org/doc/00-intro.md#autoloading).

## Versioning

[SemVer](http://semver.org/) will be followed closely.  0.x versions will introduce breaking changes, so be careful which version constraints you use.  **It's highly recommended that you use [Composer's caret operator](https://getcomposer.org/doc/articles/versions.md#caret) to ensure compatibility**; for example: `^0.18`.  This is equivalent to `>=0.18.0 <0.19.0`.

If you're only using the `CommonMarkConverter` class to convert Markdown (no other class references, custom parsers, etc.), then it should be safe to use a broader constraint like `~0.19`, `>0.19`, etc.  I personally promise to never break this specific class in any future 0.x release.
