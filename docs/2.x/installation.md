---
layout: default
title: Installation
description: Instructions on how to install the league/commonmark library
redirect_from:
  - /installation/
  - /2.0/installation/
  - /2.1/installation/
  - /2.2/installation/
  - /2.3/installation/
  - /2.4/installation/
  - /2.5/installation/
  - /2.6/installation/
  - /2.7/installation/
---

# Installation

The recommended installation method is via Composer.

```bash
composer require "league/commonmark:^2.7"
```

Ensure that you’ve set up your project to [autoload Composer-installed packages](https://getcomposer.org/doc/01-basic-usage.md#autoloading).

## Versioning

[SemVer](http://semver.org/) will be followed closely.  **It's highly recommended that you use [Composer's caret operator](https://getcomposer.org/doc/articles/versions.md#caret-version-range-) to ensure compatibility**; for example: `^2.7`.  This is equivalent to `>=2.7 <3.0`.
