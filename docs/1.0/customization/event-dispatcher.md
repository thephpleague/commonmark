---
layout: default
title: Event Dispatcher
description: How to leverage the event dispatcher to hook into the library
redirect_from:
  - /0.20/customization/document-processing/
  - /1.0/customization/document-processing/
---

# Event Dispatcher

This library includes basic event dispatcher functionality.  This makes it possible to add hook points throughout the library and third-party extensions which other code can listen for and execute code.  If you're familiar with [Symfony's EventDispatcher](https://symfony.com/doc/current/components/event_dispatcher.html) or [PSR-14](https://www.php-fig.org/psr/psr-14/) then this should be very familiar to you.

## Event Class

All events must extend from the `AbstractEvent` class:

```php
use League\CommonMark\Event\AbstractEvent;

class MyCustomEvent extends AbstractEvent {}
```

An event can have any number of methods on it which return useful information the listeners can use or modify.

## Registering Listeners

Listeners can be registered with the `Environment` using the `addEventListener()` method:

```php
public function addEventListener(string $eventClass, callable $listener, int $priority = 0)
```

The parameters for this method are:

1. The fully-qualified name of the event class you wish to observe
2. Any [PHP callable](https://www.php.net/manual/en/language.types.callable.php) to execute when that type of event is dispatched
3. An optional priority (defaults to `0`)

For example:

```php
// Telling the environment which method to call:
$customListener = new MyCustomListener();
$environment->addEventListener(MyCustomEvent::class, [$customListener, 'onDocumentParsed']);

// Or if MyCustomerListener has an __invoke() method:
$environment->addEventListener(MyCustomEvent::class, new MyCustomListener(), 10);

// Or use any other type of callable you wish!
$environment->addEventListener(MyCustomEvent::class, function (MyCustomEvent $event) {
    // TODO: Stuff
}, 10);
```

## Dispatching Events

Events can be dispatched via the `$environment->dispatch()` method which takes a single argument - an instance of `AbstractEvent` to dispatch:

```php
$environment->dispatch(new MyCustomEvent());
```

Listeners will be called in order of priority (higher priorities will be called first).  If multiple listeners have the same priority, they'll be called in the order in which they were registered.  If you'd like your listener to prevent other subsequent events from running, simply call `$event->stopPropagation()`.

Listeners may call any method on the event to get more information about the event, make changes to event data, etc.

## List of Available Events

This library supports the following default events which you can register listeners for:

### `League\CommonMark\Event\DocumentParsedEvent`

This event is dispatched once all other processing is done.  This offers extensions the opportunity to inspect and modify the [Abstract Syntax Tree](/1.0/customization/abstract-syntax-tree/) prior to rendering.

## Example

Here's an example of a listener which uses the `DocumentParsedEvent` to add an `external-link` class to external URLs:

```php
use League\CommonMark\EnvironmentInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Inline\Element\Link;

class ExternalLinkProcessor
{
    private $environment;

    public function __construct(EnvironmentInterface $environment)
    {
        $this->environment = $environment;
    }

    public function onDocumentParsed(DocumentParsedEvent $event)
    {
        $document = $event->getDocument();
        $walker = $document->walker();
        while ($event = $walker->next()) {
            $node = $event->getNode();

            // Only stop at Link nodes when we first encounter them
            if (!($node instanceof Link) || !$event->isEntering()) {
                continue;
            }

            $url = $node->getUrl();
            if ($this->isUrlExternal($url)) {
                $node->data['attributes']['class'] = 'external-link';
            }
        }
    }

    private function isUrlExternal(string $url): bool
    {
        // Only look at http and https URLs
        if (!preg_match('/^https?:\/\//', $url)) {
            return false;
        }

        $host = parse_url($url, PHP_URL_HOST);

        return $host != $this->environment->getConfig('host');
    }
}
```

And here's how you'd use it:

```php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Event\DocumentParsedEvent;

$env = Environment::createCommonMarkEnvironment();

$listener = new ExternalLinkProcessor($env);
$env->addEventListener(DocumentParsedEvent::class, [$listener, 'onDocumentParsed']);

$converter = new CommonMarkConverter(['host' => 'commonmark.thephpleague.com'], $env);

$input = 'My two favorite sites are <https://google.com> and <https://commonmark.thephpleague.com>';

echo $converter->convertToHtml($input);
```

Output (formatted for readability):

```html
<p>
    My two favorite sites are
    <a class="external-link" href="https://google.com">https://google.com</a>
    and
    <a href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a>
</p>
```
