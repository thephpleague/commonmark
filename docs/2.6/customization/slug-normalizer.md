---
layout: default
title: Slug Normalizer
description: Using the Slug Normalizer to produce unique, URL-safe text strings
redirect_from: /customization/slug-normalizer/
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

This options controls whether slugs should be unique.  Possible values include:

- `'document'` (string; **default**) - Ensures slugs are unique within a single document
- `'environment'` (string) - Ensures slugs are unique across multiple documents - see below
- `false` (boolean) - Disables unique slug generation

You might have a use case where you're converting several different Markdown documents on the same page and so you'd like to ensure that none of those documents use conflicting slugs.  In that case, you should set the `scope` option to `'environment'` to ensure that a single instance of a `MarkdownConverter` (which uses a single `Environment`) will never produce the same slug twice during its lifetime (which usually lasts the entire duration of a single HTTP request).

If you need complete control over how unique slugs are generated, make your `'instance'` implement `UniqueSlugNormalizerInterface`; otherwise, we'll simply append incremental numbers to slugs to ensure they are unique.
