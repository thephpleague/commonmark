---
layout: default
title: Mention Parser
description: The MentionParser makes it easy to parse shortened references like @colinodell and #123 to custom URLs
---

# Mention Extension

The `MentionExtension` makes it easy to parse shortened mentions and references like `@colinodell` to a Twitter URL
or `#123` to a GitHub issue URL.  You can create your own custom syntax by defining which prefix you want to use and
how to generate the corresponding URL.

## Usage

You can create your own custom syntax by supplying the configuration with an array of options that
define the starting prefix, a regular expression to match against, and any custom URL template or callable to
generate the URL.

```php
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Mention\MentionExtension;
use League\CommonMark\MarkdownConverter;

// Define your configuration
$config = [
    'mentions' => [
        // GitHub handler mention configuration.
        // Sample Input:  `@colinodell`
        // Sample Output: `<a href="https://www.github.com/colinodell">@colinodell</a>`
        'github_handle' => [
            'prefix'    => '@',
            'pattern'   => '[a-z\d](?:[a-z\d]|-(?=[a-z\d])){0,38}(?!\w)',
            'generator' => 'https://github.com/%s',
        ],
        // GitHub issue mention configuration.
        // Sample Input:  `#473`
        // Sample Output: `<a href="https://github.com/thephpleague/commonmark/issues/473">#473</a>`
        'github_issue' => [
            'prefix'    => '#',
            'pattern'   => '\d+',
            'generator' => "https://github.com/thephpleague/commonmark/issues/%d",
        ],
        // Twitter handler mention configuration.
        // Sample Input:  `@colinodell`
        // Sample Output: `<a href="https://www.twitter.com/colinodell">@colinodell</a>`
        // Note: when registering more than one mention parser with the same prefix, the first mention parser to
        // successfully match and return a properly constructed Mention object (where the URL has been set) will be the
        // the mention parser that is used. In this example, the GitHub handle would actually match first because
        // there isn't any real validation to check whether https://www.github.com/colinodell exists. However, in
        // CMS applications, you could check whether its a local user first, then check Twitter and then GitHub, etc.
        'twitter_handle' => [
            'prefix'    => '@',
            'pattern'   => '[A-Za-z0-9_]{1,15}(?!\w)',
            'generator' => 'https://twitter.com/%s',
        ],
    ],
];

// Configure the Environment with all the CommonMark parsers/renderers
$environment = new Environment($config);
$environment->addExtension(new CommonMarkCoreExtension());

// Add the Mention extension.
$environment->addExtension(new MentionExtension());

// Instantiate the converter engine and start converting some Markdown!
$converter = new MarkdownConverter($environment);
echo $converter->convert('Follow me on GitHub: @colinodell');
// Output:
// <p>Follow me on GitHub: <a href="https://www.github.com/colinodell">@colinodell</a></p>
```

## String-Based URL Templates

URL templates are perfect for situations where the identifier is inserted directly into a URL:

```text
"@colinodell" => https://www.twitter.com/colinodell
 ▲└────┬───┘                             └───┬────┘
 │     │                                     │
Prefix └───────────── Identifier ────────────┘
```

Examples of using string-based URL templates can be seen in the usage example above - you simply provide a `string` to the `generator` option.

Note that the URL template must be a string, and that the `%s` placeholder will be replaced by whatever the user enters after the prefix (in this case, `@`).  You can use any prefix, regular expression pattern (without opening/closing delimiter or modifiers), or URL template you want!

## Custom Callback-Based Parsers

Need more power than simply adding the mention inside a string based URL template? The `MentionExtension` automatically
detects if the provided generator is an object that implements `\League\CommonMark\Extension\Mention\Generator\MentionGeneratorInterface`
or a valid [PHP callable](https://www.php.net/manual/en/language.types.callable.php) that can generate a
resulting URL.

```php
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Mention\Generator\MentionGeneratorInterface;
use League\CommonMark\Extension\Mention\Mention;
use League\CommonMark\Extension\Mention\MentionExtension;
use League\CommonMark\Node\Inline\AbstractInline;
use League\CommonMark\MarkdownConverter;

// Define your configuration
$config = [
    'mentions' => [
        'github_handle' => [
            'prefix'    => '@',
            'pattern'   => '[a-z\d](?:[a-z\d]|-(?=[a-z\d])){0,38}(?!\w)',
            // The recommended approach is to provide a class that implements MentionGeneratorInterface.
            'generator' => new GithubUserMentionGenerator(), // TODO: Implement such a class yourself
        ],
        'github_issue' => [
            'prefix'    => '#',
            'pattern'   => '\d+',
            // Alternatively, if your logic is simple, you can implement an inline anonymous class like this example.
            'generator' => new class implements MentionGeneratorInterface {
                 public function generateMention(Mention $mention): ?AbstractInline
                 {
                     $mention->setUrl(\sprintf('https://github.com/thephpleague/commonmark/issues/%d', $mention->getIdentifier()));

                     return $mention;
                 }
             },
        ],
        'github_issue' => [
            'prefix'    => '#',
            'pattern'   => '\d+',
            // Any type of callable, including anonymous closures, (with optional typehints) are also supported.
            // This allows for better compatibility between different major versions of CommonMark.
            // However, you sacrifice the ability to type-check which means automated development tools
            // may not notice if your code is no longer compatible with new versions - you'll need to
            // manually verify this yourself.
            'generator' => function ($mention) {
                // Immediately return if not passed the supported Mention object.
                // This is an example of the types of manual checks you'll need to perform if not using type hints
                if (!($mention instanceof Mention)) {
                    return null;
                }

                $mention->setUrl(\sprintf('https://github.com/thephpleague/commonmark/issues/%d', $mention->getIdentifier()));

                return $mention;
            },
        ],

    ],
];

// Configure the Environment with all the CommonMark parsers/renderers
$environment = new Environment($config);
$environment->addExtension(new CommonMarkCoreExtension());

// Add the Mention extension.
$environment->addExtension(new MentionExtension());

// Instantiate the converter engine and start converting some Markdown!
$converter = new MarkdownConverter($environment);
echo $converter->convert('Follow me on Twitter: @colinodell');
// Output:
// <p>Follow me on Twitter: <a href="https://www.github.com/colinodell">@colinodell</a></p>
```

When implementing `MentionGeneratorInterface` or a simple callable, you'll receive a single `Mention` parameter and must either:

- Return the same passed `Mention` object along with setting the URL; or,
- Return a new object that extends `\League\CommonMark\Inline\Element\AbstractInline`; or,
- Return `null` (and not set a URL on the `Mention` object) if the mention isn't a match and should be skipped; not parsed.

Here's a faux-real-world example of how you might use such a generator for your application. Imagine you
want to parse `@username` into custom user profile links for your application, but only if the user exists. You could
create a class like the following which integrates with the framework your application is built on:

```php
use League\CommonMark\Extension\Mention\Generator\MentionGeneratorInterface;
use League\CommonMark\Extension\Mention\Mention;
use League\CommonMark\Inline\Element\AbstractInline;

class UserMentionGenerator implements MentionGeneratorInterface
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
        // Determine mention visibility (i.e. member privacy).
        if (!$this->currentUser->hasPermission('access profiles')) {
            $emphasis = new \League\CommonMark\Inline\Element\Emphasis();
            $emphasis->appendChild(new \League\CommonMark\Inline\Element\Text('[members only]'));
            return $emphasis;
        }

        // Locate the user that is mentioned.
        $user = $this->userRepository->findUser($mention->getIdentifier());

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

        return $mention;
    }
}
```

You can then hook this class up to a mention definition in the configuration to generate profile URLs from Markdown
mentions:

```php
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Mention\MentionExtension;
use League\CommonMark\MarkdownConverter;

// Grab your UserMentionGenerator somehow, perhaps from a DI container or instantiate it if needed
$userMentionGenerator = $container->get(UserMentionGenerator::class);

// Define your configuration
$config = [
    'mentions' => [
        'user_url_generator' => [
            'prefix'    => '@',
            'pattern'   => '[a-z0-9]+',
            'generator' => $userMentionGenerator,
        ],
    ],
];

// Configure the Environment with all the CommonMark parsers/renderers
$environment = new Environment($config);
$environment->addExtension(new CommonMarkCoreExtension());

// Add the Mention extension.
$environment->addExtension(new MentionExtension());

// Instantiate the converter engine and start converting some Markdown!
$converter = new MarkdownConverter($environment);
echo $converter->convert('You should ask @colinodell about that');

// Output (if current user has permission to view profiles):
// <p>You should ask <a href="/user/123/profile">Colin O'Dell</a> about that</p>
//
// Output (if current user doesn't have has access to view profiles):
// <p>You should ask <em>[members only]</em> about that</p>
```

## Rendering

Whenever a mention is found, a `Mention` object is added to the [document's AST](/2.3/customization/abstract-syntax-tree/).
This object extends from `Link`, so it'll be rendered as a normal `<a>` tag by default.

If you need more control over the output you can implement a [custom renderer](/2.3/customization/rendering/) for the `Mention` type
and convert it to whatever HTML you wish!
