---
layout: default
title: Event Dispatcher
---

Event Dispatcher
================

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
2. Any PHP callable to execute when that type of event is dispatched
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
