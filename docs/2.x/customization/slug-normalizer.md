---
layout: default
title: Slug Normalizer
description: Using the Slug Normalizer to produce unique, URL-safe text strings
redirect_from:
  - /customization/slug-normalizer/
  - /2.0/customization/slug-normalizer/
  - /2.1/customization/slug-normalizer/
  - /2.2/customization/slug-normalizer/
  - /2.3/customization/slug-normalizer/
  - /2.4/customization/slug-normalizer/
  - /2.5/customization/slug-normalizer/
  - /2.6/customization/slug-normalizer/
  - /2.7/customization/slug-normalizer/
---

# Slug Normalizer

"Slugs" are strings used within `href`, `name`, and `id` HTML attributes to identify particular elements within a document.

Some extensions (like the `HeadingPermalinkExtension`) need the ability to convert user-provided text into these URL-safe slugs while also ensuring that these are unique throughout the generated HTML.  The `Environment` provides a pre-built normalizer you can use for this purpose.

## Usage

You can obtain a reference to the built-in slug normalizer by calling `$environment->getSlugNormalizer()`;

To use this within your extension, have your parser/renderer/whatever implement `EnvironmentAwareInterface` and then implement the corresponding `setEnvironment` method like this:

```php

use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Environment\EnvironmentAwareInterface;

class MyCustomParserOrRenderer implements EnvironmentAwareInterface
{
    private $slugNormalizer;

    public function setEnvironment(EnvironmentInterface $environment): void
    {
        $this->slugNormalizer = $environment->getSlugNormalizer();
    }
}
```

You can then call `$this->slugNormalizer->normalize($text)` as needed.

## Configuration

The `slug_normalizer` configuration section allows you to adjust the following options:

### `instance`

You can change the string that is used as the "slug" by setting the `instance` option to any class that implements `TextNormalizerInterface`.
We provide a simple `SlugNormalizer` by default, but you may want to plug in a different library or create your own normalizer instead.

For example, if you'd like each slug to be an MD5 hash, you could create a class like this:

```php
use League\CommonMark\Normalizer\TextNormalizerInterface;

final class MD5Normalizer implements TextNormalizerInterface
{
    public function normalize(string $text, $context = null): string
    {
        return md5($text);
    }
}
```

And then configure it like this:

```php
$config = [
    'slug_normalizer' => [
        // ... other options here ...
        'instance' => new MD5Normalizer(),
    ],
];
```

Or you could use [PHP's anonymous class feature](https://www.php.net/manual/en/language.oop5.anonymous.php) to define the generator's behavior without creating a new class file:

```php
$config = [
    'slug_normalizer' => [
        // ... other options here ...
        'instance' => new class implements TextNormalizerInterface {
            public function normalize(string $text, $context = null): string
            {
                // TODO: Implement your code here
            }
        },
    ],
];
```

### `max_length`

This can be configured to limit the length of that slug to prevent overly-long values. By default, that limit is `255` characters. You may set this to any positive integer, or `0` for no limit.

(Note that generated slugs might be slightly longer than this "limit" if the `unique` option is enabled and the slug generator detects a duplicate slug and needs to add a suffix to make it unique.)

### `unique`

This option controls whether slugs should be unique.  Possible values include:

- `'document'` (string; **default**) - Ensures slugs are unique within a single document
- `'environment'` (string) - Ensures slugs are unique across multiple documents - see below
- `false` (boolean) - Disables unique slug generation

You might have a use case where you're converting several different Markdown documents on the same page and so you'd like to ensure that none of those documents use conflicting slugs.  In that case, you should set the `scope` option to `'environment'` to ensure that a single instance of a `MarkdownConverter` (which uses a single `Environment`) will never produce the same slug twice during its lifetime (which usually lasts the entire duration of a single HTTP request).

If you need complete control over how unique slugs are generated, make your `'instance'` implement `UniqueSlugNormalizerInterface`; otherwise, we'll simply append incremental numbers to slugs to ensure they are unique.

### `reserved`

Perhaps the page surrounding your rendered Markdown already uses certain HTML IDs - maybe your site header has `id="site-logo"` or your comment section uses `id="comments"`.  If a Markdown heading happens to normalize to one of those same slugs you'd end up with duplicate IDs on the page.

There are two different levers for avoiding this kind of collision:

- The [`heading_permalink/id_prefix` option](/2.x/extensions/heading-permalinks/#id_prefix) prepends a prefix to **every** generated ID.  Its default value of `'content'` acts as a namespace: as long as nothing else on your page uses IDs starting with `content-`, generated IDs like `content-comments` cannot collide - so with the default prefix you typically don't need this option.
- The `reserved` option protects only the **specific** slugs you list, leaving all other IDs clean and prefix-free.  This is useful when you've set `id_prefix` to an empty string because you want pretty anchors like `#introduction` instead of `#content-introduction`.

The `reserved` option accepts a list of slugs that should be treated as if they were already used.  Any Markdown content that would normally generate one of these slugs will automatically receive an incremental numeric suffix instead, exactly as if a duplicate heading had appeared earlier in the document:

```php
$config = [
    'heading_permalink' => [
        // No prefixes, so generated IDs and fragments exactly match the heading slugs
        'id_prefix' => '',
        'fragment_prefix' => '',
    ],
    'slug_normalizer' => [
        'reserved' => ['site-logo', 'comments'],
    ],
];
```

With this configuration, a `## Comments` heading would be given a slug of `comments-1` instead of `comments`, avoiding a collision with the page's own `id="comments"` element.

Note that reserved slugs are compared against the final, normalized slug (before any `heading_permalink/id_prefix` is applied), so they should be listed in that same normalized form: `comments`, not `Comments!`.  This also means that if you do use an `id_prefix`, the prefix should not be included in the reserved entries: if your page contains a prefixed ID like `content-comments` while using the default prefix, reserve the unprefixed slug instead: `'reserved' => ['comments']`.

This option only applies when the `unique` option is enabled and the `instance` doesn't implement `UniqueSlugNormalizerInterface` itself.  (If you bring your own unique normalizer, honoring reserved slugs is up to your implementation.)
