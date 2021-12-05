---
layout: default
title: Event Dispatcher
description: How to leverage the event dispatcher to hook into the library
---

# Event Dispatcher

This library includes basic, [PSR-14](https://www.php-fig.org/psr/psr-14/)-compliant event dispatcher functionality.  This makes it possible to add hook points throughout the library and third-party extensions which other code can listen for and execute code.

## Event Class

Any [PSR-14 compliant event](https://www.php-fig.org/psr/psr-14/#events) can be used, though we also provide an `AbstractEvent` class you can use to easily create your own events:

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

Events can be dispatched via the `$environment->dispatch()` method which takes a single argument - the event object to dispatch:

```php
$environment->dispatch(new MyCustomEvent());
```

Listeners will be called in order of priority (higher priorities will be called first).  If multiple listeners have the same priority, they'll be called in the order in which they were registered.  If you'd like your listener to prevent other subsequent events from running, simply call `$event->stopPropagation()`.

Listeners may call any method on the event to get more information about the event, make changes to event data, etc.

## List of Available Events

This library supports the following default events which you can register listeners for:

### `League\CommonMark\Event\DocumentPreParsedEvent`

This event is dispatched just before any processing is done. It can be used to pre-populate reference map of a document or manipulate the Markdown contents before any processing is performed.

### `League\CommonMark\Event\DocumentParsedEvent`

This event is dispatched once all other processing is done.  This offers extensions the opportunity to inspect and modify the [Abstract Syntax Tree](/2.0/customization/abstract-syntax-tree/) prior to rendering.

### `League\CommonMark\Event\DocumentPreRenderEvent`

This event is dispatched by the renderer just before rendering begins.  Like with `DocumentParsedEvent`, this offers extensions the opportunity to inspect and modify the [Abstract Syntax Tree](/2.0/customization/abstract-syntax-tree/) prior to rendering, but with the added knowledge of which format is being rendered to (e.g. `html`).

### `League\CommonMark\Event\DocumentRenderedEvent`

This event is dispatched once the rendering step has been completed, just before the output is returned.  The final output can be adjusted at this point or additional metadata can be attached to the return object.

## Bring Your Own PSR-14 Event Dispatcher

Although this library provides PSR-14 compliant event dispatching out-of-the-box, you may want to use your own PSR-14 event dispatcher instead.  This is possible as long as that third-party library both:

 1. Implements the PSR-14 `EventDispatcherInterface`; and,
 2. Allows you to register additional `ListenerProviderInterface` instances with that dispatcher library

Not all libraries support this so please check carefully!  Assuming yours does, delegating all the event behavior to that library can be done with two steps:

First, call the `setEventDispatcher()` method on the `Environment` to register that other implementation.  With that done, any calls to `Environment::dispatch()` will be passed through to that other dispatcher.  But we still need to let that dispatcher know about the events registered by CommonMark extensions, otherwise nothing will happen when events are dispatched.

Because the `Environment` implements PSR-14's `ListenerProviderInterface` you'll also need to pass the configured `Environment` object to your event dispatcher so that it becomes aware of those available events.

## Example

Here's an example of a listener which uses the `DocumentParsedEvent` to add an `external-link` class to external URLs:

```php
use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;

class ExternalLinkProcessor
{
    private $environment;

    public function __construct(EnvironmentInterface $environment)
    {
        $this->environment = $environment;
    }

    public function onDocumentParsed(DocumentParsedEvent $event): void
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
                $node->data->append('attributes/class', 'external-link');
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

        return $host != $this->environment->getConfiguration()->get('host');
    }
}
```

And here's how you'd use it:

```php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Event\DocumentParsedEvent;

$env = new Environment();

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
