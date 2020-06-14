---
layout: default
title: Mention Parser
description: The MentionParser makes it easy to parse shortened references like @colinodell and #123 to custom URLs
redirect_from: /extensions/mentions/
---

# Mention Extension

The `MentionExtension` makes it easy to parse shortened mentions and references like `@colinodell` to a Twitter URL
or `#123` to a GitHub issue URL. The extension itself, does not do much other than register defined mention
configurations from the environment.

## Usage

You can create your own custom syntax by supplying the environment configuration with an array of mentions that
contain the starting symbol, a regular expression to match against, and any custom string template or callable to
generate the URL.

```php
<?php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\Mention\MentionExtension;

// Obtain a pre-configured Environment with all the CommonMark parsers/renderers ready-to-go.
$environment = Environment::createCommonMarkEnvironment();

// Add the Mention extension.
$environment->addExtension(new MentionExtension());

// Set your configuration.
$config = [
    // All mentions must reside in the "mentions" config key.
    // This is just an example of what MentionExtension::registerTwitterHandle
    // registers. If no mentions are provided in configuration the extension does nothing.
    'mentions' => [
        // A unique key.
        'twitter_handler' => [
            // Required - The starting symbol for the inline parser to find.
            'symbol'    => '@',
            // Required - The regular expression the inline parser must match.
            'regex'     => '/^[A-Za-z0-9_]{1,15}(?!\w)/',
            // Required - The string template or callable that will be used to generate the mention URL.
            // You can learn more about callable generators below.
            'generator' => 'https://twitter.com/%s',
        ],
    ],
];

// Instantiate the converter engine and start converting some Markdown!
$converter = new CommonMarkConverter($config, $environment);
echo $converter->convertToHtml('Follow me on Twitter: @colinodell');
```

Note that the URL template (third argument) must be a string, and that the `%s` placeholder will be replaced by whatever the user enters after the symbol (in this case, `@`).  You can use any symbol, regex pattern, or URL template you want!

## Pre-configured mention parsers

This library includes a few static methods on the `MentionExtension` class which can be invoked to register common
mentions with the environment configuration:

```php
use League\CommonMark\Environment;
use League\CommonMark\Extension\Mention\MentionExtension;

// Obtain a pre-configured Environment with all the CommonMark parsers/renderers ready-to-go.
$environment = Environment::createCommonMarkEnvironment();

// Add the Mention extension.
$environment->addExtension(new MentionExtension());

// This registers Twitter handler mention configuration.
// Sample Input:  `@colinodell`
// Sample Output: `<a href="https://www.twitter.com/colinodell">@colinodell</a>`
MentionExtension::registerTwitterHandle($environment);

// This registers GitHub handler mention configuration.
// Sample Input:  `@colinodell`
// Sample Output: `<a href="https://www.github.com/colinodell">@colinodell</a>`
// Note: registering both the Twitter and GitHub handler mentions in the same environment will result in a \RuntimeException
// being thrown: Only one type of mention symbol (i.e. @ or #) can be registered per the environment configuration.
MentionExtension::registerGitHubHandle($environment);

// This registers GitHub issue mention configuration.
// Sample Input:  `#473`
// Sample Output: `<a href="https://github.com/thephpleague/commonmark/issues/473">#473</a>`
MentionExtension::registerGitHubIssue($environment, 'thephpleague/commonmark');
```

## Custom Callback-Based Parsers

Need more power than simply adding the mention inside a string based URL template? The `MentionExtension` automatically
detects if the provided generator is a valid [PHP callable](https://www.php.net/manual/en/language.types.callable.php)
to generate the resulting URL. While this can be a method on a dedicated class (complete with typehints), it can also
be a simple closure that doesn't require any typehints at all:

```php
use League\CommonMark\Extension\Mention\Mention;

$callback = function ($mention) {
    // Checking the type inside the callback gives integrators more freedom should they need to support newer
    // objects or features down the road.
    if ($mention instanceof Mention) {
        $mention->setUrl('...');
    }
};
```

This callable will receive a single `Mention` parameter and must either:
  - Set the URL on the passed `Mention` object.
  - Return a new object that extends `\League\CommonMark\Inline\Element\AbstractInline`; or,
  - Return `null` (and not set a URL on the `Mention` object) if the mention isn't a match and should be skipped; not parsed.

Here's an example of how you might use a callback-based parser.  Imagine you want to parse `@username` into custom user profile
links for your application, but only if the user exists.  You could create a class like this which integrates with your framework:

```php
use League\CommonMark\Extension\Mention\Generator\MentionGeneratorInterface;use League\CommonMark\Extension\Mention\Mention;use League\CommonMark\Inline\Element\AbstractInline;use League\CommonMark\Inline\Element\Emphasis;use League\CommonMark\Inline\Element\Text;

class UserUrlGenerator implements MentionGeneratorInterface
{
    private $currentUser;
    private $userRepository;
    private $router;

    public function __construct (AccountInterface $currentUser, UserRepository $userRepository, Router $router)
    {
        $this->currentUser = $currentUser;
        $this->userRepository = $userRepository;
        $this->router = $router;
    }

    public function generateMention(Mention $mention): ?AbstractInline
    {
        // Determine mention visibility.
        if (!$this->currentUser->hasPermission('access profiles')) {
            $emphasis = new Emphasis();
            $emphasis->appendChild(new Text('[members only]'));
            return $emphasis;
        }

        // Locate the user that is mentioned.
        $user = $this->userRepository->findUser($mention->getHandle());

        // The mention isn't valid if the user does not exist.
        if (!$user) {
            return null;
        }

        // Change the label.
        $mention->setLabel($user->getFullName());

        // Use the path to their profile as the URL, typecasting to a string in case the service returns
        // a __toString object; otherwise you will need to figure out a way to extract the string URL
        // from the service.
        $mention->setUrl((string) $this->router->generate('user_profile', ['id' => $user->getId()]));
    }
}
```

You can then hook this class up to a mention definition in the configuration to generate profile URLs from Markdown
mentions:

```php
<?php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\Mention\MentionExtension;

// Grab your UserUrlGenerator somehow, perhaps from a DI container or instantiate it if needed
$userUrlGenerator = $container->get(UserUrlGenerator::class);

// Obtain a pre-configured Environment with all the CommonMark parsers/renderers ready-to-go
$environment = Environment::createCommonMarkEnvironment();

// Add the Mention extension.
$environment->addExtension(new MentionExtension());

// Set your configuration.
$config = [
    'mentions' => [
        'user_url_generator' => [
            'symbol'    => '@',
            'regex'     => '/^[a-z0-9]+/i',
            'generator' => [$userUrlGenerator, 'generateMention'],
        ],
    ],
];

// Instantiate the converter engine and start converting some Markdown!
$converter = new CommonMarkConverter($config, $environment);
echo $converter->convertToHtml('You should ask @colinodell about that');

// Output (if current user has permission to view profiles):
// <p>You should ask <a href="/user/123/profile">Colin O'Dell</a> about that</p>
//
// Output (if current user doesn't have has access to view profiles):
// <p>You should ask <em>[members only]</em> about that</p>
```
