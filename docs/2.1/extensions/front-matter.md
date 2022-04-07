---
layout: default
title: Front Matter Extension
description: The Front Matter extension automatically parses YAML front matter from your Markdown.
---

# Front Matter Extension

The `FrontMatterExtension` adds the ability to parse YAML front matter from the Markdown document and include that in the return result.

## Installation

This extension is bundled with `league/commonmark`. This library can be installed via Composer:

```bash
composer require league/commonmark
```

See the [installation](/2.1/installation/) section for more details.

You will also need to install `symfony/yaml` or the [YAML extension for PHP](https://www.php.net/manual/book.yaml.php) to use this extension. For `symfony/yaml`:

```bash
composer require symfony/yaml
```

(You can use any version of `symfony/yaml` 2.3 or higher, though we recommend using 4.0 or higher.)

## Front Matter Syntax

This extension follows the [Jekyll Front Matter syntax](https://jekyllrb.com/docs/front-matter/). The front matter must be the first thing in the file and must take the form of valid YAML set between triple-dashed lines. Here is a basic example:

```markdown
---
layout: post
title: I Love Markdown
tags:
  - test
  - example
---

# Hello World!
```

This will produce a front matter array similar to this:

```php
$parsedFrontMatter = [
    'layout' => 'post',
    'title' => 'I Love Markdown',
    'tags' => [
        'test',
        'example',
    ],
];
```

And the HTML output will only contain the one heading:

```html
<h1>Hello World!</h1>
```

## Usage

Configure your `Environment` as usual and add the `FrontMatterExtension`:

```php
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\MarkdownConverter;

// Define your configuration, if needed
$config = [];

// Configure the Environment with all the CommonMark parsers/renderers
$environment = new Environment($config);
$environment->addExtension(new CommonMarkCoreExtension());

// Add the extension
$environment->addExtension(new FrontMatterExtension());

// Instantiate the converter engine and start converting some Markdown!
$converter = new MarkdownConverter($environment);

// A sample Markdown file with some front matter:
$markdown = <<<MD
---
layout: post
title: I Love Markdown
tags:
  - test
  - example
---

# Hello World!
MD;

$result = $converter->convertToHtml($markdown);

// Grab the front matter:
if ($result instanceof RenderedContentWithFrontMatter) {
    $frontMatter = $result->getFrontMatter();
}

// Output the HTML using any of these:
echo $result;               // implicit string cast
// or:
echo (string) $result;      // explicit string cast
// or:
echo $result->getContent();
```

### Parsing Front Matter Only

You don't have to parse the entire file (including all the Markdown) if you only want the front matter.  You can either instantiate the front matter parser yourself and call it directly, like this:

```php
use League\CommonMark\Extension\FrontMatter\Data\LibYamlFrontMatterParser;
use League\CommonMark\Extension\FrontMatter\Data\SymfonyYamlFrontMatterParser;
use League\CommonMark\Extension\FrontMatter\FrontMatterParser;

$markdown = '...'; // TODO: Load some Markdown content somehow

// For `symfony/yaml`
$frontMatterParser = new FrontMatterParser(new SymfonyYamlFrontMatterParser());
// For YAML extension
$frontMatterParser = new FrontMatterParser(new LibYamlFrontMatterParser());
$result = $frontMatterParser->parse($markdown);

var_dump($result->getFrontMatter()); // The parsed front matter
var_dump($result->getContent()); // Markdown content without the front matter
```

Or you can use the `getFrontMatterParser()` method from the extension:

```php
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;

$markdown = '...'; // TODO: Load some Markdown content somehow

$frontMatterExtension = new FrontMatterExtension();
$result = $frontMatterExtension->getFrontMatterParser()->parse($markdown);

var_dump($result->getFrontMatter()); // The parsed front matter
var_dump($result->getContent()); // Markdown content without the front matter
```

This latter approach may be more convenient if you have already instantiated a `FrontMatterExtension` object you're adding to the `Environment` somewhere and just want to call that.
