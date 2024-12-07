---
layout: default
title: Embed Extension
description: The EmbedExtension supports embedding rich content from other websites.
---

# Embed Extension

This extension can embed rich content (like videos, tweets, etc.) from other websites.

The syntax is very simple - simply place any `https://` URL on its own line like this:

```md
Check out this video!

https://www.youtube.com/watch?v=dQw4w9WgXcQ
```

If the link points to embeddable content, it will be replaced with the rich HTML needed to embed it:

```html
<p>Check out this video:</p>
<iframe width="200" height="113" src="https://www.youtube.com/embed/dQw4w9WgXcQ?feature=oembed" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
```

## Installation

This extension is bundled with `league/commonmark`. This library can be installed via Composer:

```bash
composer require league/commonmark
```

You'll also need to install a third-party [OEmbed](https://www.oembed.com/) library - see the [**Adapter**](#adapter) section below.

## Usage

Configure your `Environment` as usual and add the `EmbedExtension` provided by this package:

```php
use Embed\Embed;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Embed\EmbedExtension;
use League\CommonMark\MarkdownConverter;

// Define your configuration
$config = [
    'embed' => [
        'adapter' => new OscaroteroEmbedAdapter(), // See the "Adapter" documentation below
        'allowed_domains' => ['youtube.com', 'twitter.com', 'github.com'],
        'fallback' => 'link',
    ],
];

// Configure the Environment with all whatever other extensions you want
$environment = new Environment($config);
$environment->addExtension(new CommonMarkCoreExtension());

// Add this extension
$environment->addExtension(new EmbedExtension());

// Instantiate the converter engine and start converting some Markdown!
$converter = new MarkdownConverter($environment);
echo $converter->convert('https://www.youtube.com/watch?v=dQw4w9WgXcQ');
```

## Configuration

This extension supports the following configuration options under the `embed` configuration:

### `adapter` option

Any instance of `EmbedAdapterInterface` - see the "**[Adapter](#adapter)**" section below.

### `allowed_domains` option

This option defines a list of hosts that you wish to allow embedding content from. For example, setting this to
`['youtube.com']` would only allow videos from YouTube to be embedded.
It's extremely important that you only include websites you trust since they'll be providing HTML that is directly embedded in your website.

Any subdomains of these domains will also be allowed. For example, `['youtube.com']` would allow embedding from `youtube.com` or `www.youtube.com`.

As an additional safety measure, we recommend that you also use a [Content Security Policy (CSP)](https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP)
to prevent unexpected content from being embedded.

By default, this option is an empty array (`[]`), which means that all domains are allowed.

### `fallback` option

This options defines the behavior when a URL cannot be embedded, either because it's not in the list of `allowed_domains`,
or because the `adapter` could not find embeddable content for that URL.

There are two possible values for this option:

- `'link'` - the URL will be kept in the document as a link (**default**)
-`'remove'` - the URL will be completely removed from the document

## Adapter

`league/commonmark` doesn't know how to obtain the embeddable HTML for a given URL - this must be done by an external library.

### `embed/embed` Adapter

We do provide an adapter for the popular [`embed/embed`](https://github.com/oscarotero/Embed) library. if you'd like to use that.  We like this library
because it supports fetching multiple URLs in parallel, which is ideal for performance, and it supports a wide range
of embeddable content.

To use that library, you'll need to `composer install embed/embed` and then pass `new OscaroteroEmbedAdapter()` as the `adapter`
configuration option, as shown in the [**Usage**](#usage) section above.

Note: `embed/embed` *requires* a PSR-17 implementation to be installed.  If you do not have one installed, the library will not work.  By default these libraries are detected automatically:

- [laminas/laminas-diactoros](https://github.com/laminas/laminas-diactoros)
- [guzzle/psr7](https://github.com/guzzle/psr7)
- [nyholm/psr7](https://github.com/Nyholm/psr7)
- [sunrise/http-message](https://github.com/sunrise-php/http-message)

Need to customize the maximum width/height of the embedded content? You can do that by instantiating the service provided by
`embed/embed`, [configuring it as needed](https://github.com/oscarotero/Embed#settings), and passing that customized instance into the adapter:

```php
use Embed\Embed;
use League\CommonMark\Extension\Embed\Bridge\OscaroteroEmbedAdapter;

// Configure the Embed library itself
$embedLibrary = new Embed();
$embedLibrary->setSettings([
    'oembed:query_parameters' => [
        'maxwidth' => 800,
        'maxheight' => 600,
    ],
    'twitch:parent' => 'example.com',
    'facebook:token' => '1234|5678',
    'instagram:token' => '1234|5678',
    'twitter:token' => 'asdf',
]);

// Inject it into our adapter
$config = [
    'adapter' => new OscaroteroEmbedAdapter($embedLibrary),
];

// Instantiate your CommonMark environment and converter like usual
// ...
```

### Custom Adapter

If you prefer to use a different library, you'll need to implement our `EmbedAdapterInterface` yourself with
[whatever OEmbed library](https://packagist.org/?tags=oembed) you choose.

## Tips

If you need to wrap the HTML in a container tag, consider using the [`HtmlDecorator` renderer](/2.5/customization/rendering/#wrapping-elements-with-htmldecorator):

```php
$environment->addRenderer(Embed::class, new HtmlDecorator(new EmbedRenderer(), 'div', ['class' => 'embeded-content']));
```
