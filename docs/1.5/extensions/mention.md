---
layout: default
title: Mention Parser
description: The MentionParser makes it easy to parse shortened references like @colinodell and #123 to custom URLs
---

# Mentions

Although not a full extension, the `MentionParser` makes it easy to parse shortened mentions and references like `@colinodell` to a Twitter URL or `#123` to a GitHub issue URL.  You can create your own custom syntax by choosing the starting symbol, a regular expression to match against, and any custom URL generator you wish.  Pre-built mention parsers are also available for use.


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

## Advanced Custom Parsers

Need more power than simply adding the mention inside a URL template?  The main constructor of `MentionParser` allows you to pass in any `callable` to generate a URL from the given mention.

For example, if you wanted to check whether a user exists in your database, you could create a class like this which integrates with your framework:

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

    public function getProfileUrl(string $username, string $symbol): ?string
    {
        $user = $this->userRepository->findUser($username);
        if ($user === null) {
            return null;
        }

        $profileUrl = $this->router->generate('user_profile', ['id' => $user->getId()]);

        // You could simply return the URL like this:
        return $profileUrl;

        // Or your could even return your own customized `Link` node:
        return new Link($profileUrl, $symbol.$username, "View $username's profile");
    }
}
```

And then use this class to generate profile URLs from Markdown mentions:

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
$environment->addInlineParser(new MentionParser('@', '/^[a-z0-9]+/i', [$userUrlGenerator, 'getProfileUrl']));

// Instantiate the converter engine and start converting some Markdown!
$converter = new CommonMarkConverter($config, $environment);
echo $converter->convertToHtml('You should ask @colinodell about that');
```

A few notes:

 - The third argument to `new MentionParser()` can be any valid [PHP callable](https://www.php.net/manual/en/language.types.callable.php)
 - That callable will receive two parameters:
   - A `string` containing the mention that matches your regex
   - A `string` containing the symbol
 - That callable must return one of the following:
   - A `string` containing the URL
   - An `AbstractInline` node that should be used as the link (great if you need to store custom attributes for future rendering); this could be a `Link` or something custom that extends `Link`
   - `null` if no mention link should be generated

