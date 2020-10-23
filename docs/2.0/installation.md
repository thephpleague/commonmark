---
layout: default
title: Installation
description: Instructions on how to install the league/commonmark library
---

# Installation

The recommended installation method is via Composer.

```bash
composer require "league/commonmark:^2.0"
```

Since this version is still a work in progress, you will need to include packages of dev stability. If you see an error like this you need to change your stability requirements.

```bash
Your requirements could not be resolved to an installable set of packages.

Problem 1
- The requested package league/commonmark ^2.0 is satisfiable by league/commonmark[2.0.x-dev] but these conflict with your requirements or minimum-stability.
```

The easiest way to do this, is to create a file in your project root, called `composer.json`

Put the following code in it and save:

```json
{
  "require-dev": {
    "league/commonmark": "^2.0@dev",
    "dflydev/dot-access-data": "^3.0@dev"
  }
}
```

When the file is set up, run the following command and you are done.

```bash
composer install
```

Ensure that you’ve set up your project to [autoload Composer-installed packages](https://getcomposer.org/doc/01-basic-usage.md#autoloading).

## Versioning

[SemVer](http://semver.org/) will be followed closely.  **It's highly recommended that you use [Composer's caret operator](https://getcomposer.org/doc/articles/versions.md#caret-version-range-) to ensure compatibility**; for example: `^2.0`.  This is equivalent to `>=2.0 <3.0`.
