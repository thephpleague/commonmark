---
layout: default
title: Mention Parser
description: The MentionParser makes it easy to parse shortened references like @colinodell and #123 to custom URLs
---

# Mentions

Although not a full extension, the `MentionParser` makes it easy to parse shortened mentions and references like `@colinodell` to a Twitter URL or `#123` to a GitHub issue URL.  You can create your own custom syntax by choosing the starting symbol, a regular expression to match against, and any custom URL generator you wish.  Pre-built mention parsers are also available for use.


## Installation

This is bundled with `league/commonmark`. This library can be installed via Composer:

~~~bash
composer require league/commonmark
~~~

See the [installation](/1.5/installation/) section for more details.

## Usage

An instance of `MentionParser` can be added to any environment like so:

```php
<?php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\Mention\MentionParser;

// Obtain a pre-configured Environment with all the CommonMark parsers/renderers ready-to-go
$environment = Environment::createCommonMarkEnvironment();

// Add your mention parser
$environment->addInlineParser(MentionParser::createTwitterHandleParser());

// Instantiate the converter engine and start converting some Markdown!
$converter = new CommonMarkConverter($config, $environment);
echo $converter->convertToHtml('Follow me on Twitter: @colinodell');
```

## Prebuilt Mention Parsers

The following static constructors allow you to easily parse some common types of mentions:

| Static constructor                                  | Sample Input  | Sample Output |
| --------------------------------------------------- | ------------- | -------------------------------------------------------------------------- |
| `MentionParser::createTwitterHandleParser()`        | `@colinodell` | `<a href="https://www.twitter.com/colinodell">@colinodell</a>`             |
| `MentionParser::createGitHubHandleParser()`         | `@colinodell` | `<a href="https://www.github.com/colinodell">@colinodell</a>`              |
| `MentionParser::createGitHubIssueParser($repoName)` | `#473`        | `<a href="https://github.com/thephpleague/commonmark/issues/473">#473</a>` |

## Custom Template-Based Parsers

Need to create a custom mention parser where the mentioned text is inserted directly into the URL?  The `MentionParser::createWithStringTemplate()` static constructor makes this a snap:

```php
<?php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\Mention\MentionParser;

// Obtain a pre-configured Environment with all the CommonMark parsers/renderers ready-to-go
$environment = Environment::createCommonMarkEnvironment();

// Add your mention parser
$environment->addInlineParser(MentionParser::createWithStringTemplate('@', '/^[a-z0-9]+/i', 'https://www.example.com/%s'));

// Instantiate the converter engine and start converting some Markdown!
$converter = new CommonMarkConverter($config, $environment);
echo $converter->convertToHtml('Follow me on Twitter: @colinodell');
```

Note that the URL template (third argument) must be a string, and that the `%s` placeholder will be replaced by whatever the user enters after the symbol (in this case, `@`).  You can use any symbol, regex pattern, or URL template you want!

## Custom Callback-Based Parsers

Need more power than simply adding the mention inside a URL template?  The `MentionParser::createWithCallback()` static constructor gives you more control over the resulting links by supplying any valid [PHP callable](https://www.php.net/manual/en/language.types.callable.php) to generate the resulting URL:

```php
$callback = function (string $handle, string &$label, string $symbol): ?string {
    // ...
};
```

This callable will receive three string parameters:

  - A `string` containing the parsed "handle" (everything after the symbol that matched you regex)
  - A `string` containing the proposed label text (the symbol and handle concatenated together)
  - A `string` containing the symbol

The callable must return either:
  - A `string` containing the URL to use; or,
  - `null` if no `Link` should be generated

Note that the `$label` is passed by-reference - this allows you to also customize the label if desired.

Here's an example of how you might use a callback-based parser.  Imagine you want to parse `@username` into custom user profile links for your application, but only if the user exists.  You could create a class like this which integrates with your framework:

```php
use League\CommonMark\Inline\Element\Link;

class UserUrlGenerator
{
    private $userRepository;
    private $router;

    public function __construct (UserRepository $userRepository, Router $router)
    {
        $this->userRepository = $userRepository;
        $this->router = $router;
    }

    public function getProfileUrl(string $handle, string &$label, string $symbol): ?string
    {
        $user = $this->userRepository->findUser($handle);

        // Don't generate a link if the user does not exist
        if ($user === null) {
            return null;
        }

        // Change the label (possible because it is passed by reference)
        $label = $user->getFullName();

        // Use the path to their profile as the URL
        return $this->router->generate('user_profile', ['id' => $user->getId()]);
    }
}
```

You can then hook this class up to a `MentionParser` to generate profile URLs from Markdown mentions:

```php
<?php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\Mention\MentionParser;

// Obtain a pre-configured Environment with all the CommonMark parsers/renderers ready-to-go
$environment = Environment::createCommonMarkEnvironment();

// Grab your UserUrlGenerator somehow, perhaps from a DI container or instantiate it if needed
$userUrlGenerator = $container->get(UserUrlGenerator::class);

// Add your mention parser
$environment->addInlineParser(MentionParser::createWithCallback('@', '/^[a-z0-9]+/i', [$userUrlGenerator, 'getProfileUrl']));

// Instantiate the converter engine and start converting some Markdown!
$converter = new CommonMarkConverter($config, $environment);
echo $converter->convertToHtml('You should ask @colinodell about that');

// Output:
// <p>You should ask <a href="/user/123/profile">Colin O'Dell</a> about that</p>
```
