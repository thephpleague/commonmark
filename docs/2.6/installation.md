---
layout: default
title: Installation
description: Instructions on how to install the league/commonmark library
redirect_from: /installation/
---

# Installation

The recommended installation method is via Composer.

```bash
composer require "league/commonmark:^2.6"
```

Ensure that you’ve set up your project to [autoload Composer-installed packages](https://getcomposer.org/doc/01-basic-usage.md#autoloading).

## Versioning

[SemVer](http://semver.org/) will be followed closely.  **It's highly recommended that you use [Composer's caret operator](https://getcomposer.org/doc/articles/versions.md#caret-version-range-) to ensure compatibility**; for example: `^2.6`.  This is equivalent to `>=2.6 <3.0`.
