<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Util;

/**
 * Array collection
 *
 * Provides a wrapper around a standard PHP array.
 *
 * @internal
 */
class ArrayCollection implements \IteratorAggregate, \Countable, \ArrayAccess
{
    /**
     * @var array
     */
    private $elements;

    /**
     * Constructor
     *
     * @param array $elements
     */
    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    /**
     * @return mixed
     */
    public function first()
    {
        return \reset($this->elements);
    }

    /**
     * @return mixed
     */
    public function last()
    {
        return \end($this->elements);
    }

    /**
     * Retrieve an external iterator
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->elements);
    }

    /**
     * @param mixed $element
     *
     * @return bool
     *
     * @deprecated
     */
    public function add($element): bool
    {
        @trigger_error(sprintf('The "%s:%s" method is deprecated since league/commonmark 1.4, use "%s" instead.', self::class, 'add()', '$collection[] = $value'), E_USER_DEPRECATED);

        $this->elements[] = $element;

        return true;
    }

    /**
     * @param mixed $key
     * @param mixed $value
     *
     * @deprecated
     */
    public function set($key, $value)
    {
        @trigger_error(sprintf('The "%s:%s" method is deprecated since league/commonmark 1.4, use "%s" instead.', self::class, 'set()', '$collection[$key] = $value'), E_USER_DEPRECATED);

        $this->offsetSet($key, $value);
    }

    /**
     * @param mixed $key
     *
     * @return mixed
     *
     * @deprecated
     */
    public function get($key)
    {
        @trigger_error(sprintf('The "%s:%s" method is deprecated since league/commonmark 1.4, use "%s" instead.', self::class, 'get()', '$collection[$key]'), E_USER_DEPRECATED);

        return $this->offsetGet($key);
    }

    /**
     * @param mixed $key
     *
     * @return mixed|null
     *
     * @deprecated
     */
    public function remove($key)
    {
        @trigger_error(sprintf('The "%s:%s" method is deprecated since league/commonmark 1.4, use "%s" instead.', self::class, 'remove()', 'unset($collection[$key])'), E_USER_DEPRECATED);

        if (!\array_key_exists($key, $this->elements)) {
            return;
        }

        $removed = $this->elements[$key];
        unset($this->elements[$key]);

        return $removed;
    }

    /**
     * @return bool
     *
     * @deprecated
     */
    public function isEmpty(): bool
    {
        @trigger_error(sprintf('The "%s:%s" method is deprecated since league/commonmark 1.4, use "%s" instead.', self::class, 'isEmpty()', 'count($collection) === 0'), E_USER_DEPRECATED);

        return empty($this->elements);
    }

    /**
     * @param mixed $element
     *
     * @return bool
     *
     * @deprecated
     */
    public function contains($element): bool
    {
        @trigger_error(sprintf('The "%s:%s" method is deprecated since league/commonmark 1.4, use "%s" instead.', self::class, 'contains()', 'in_array($value, $collection->toArray(), true)'), E_USER_DEPRECATED);

        return \in_array($element, $this->elements, true);
    }

    /**
     * @param mixed $element
     *
     * @return mixed|false
     *
     * @deprecated
     */
    public function indexOf($element)
    {
        @trigger_error(sprintf('The "%s:%s" method is deprecated since league/commonmark 1.4, use "%s" instead.', self::class, 'indexOf()', 'array_search($value, $collection->toArray(), true)'), E_USER_DEPRECATED);

        return \array_search($element, $this->elements, true);
    }

    /**
     * @param mixed $key
     *
     * @return bool
     *
     * @deprecated
     */
    public function containsKey($key): bool
    {
        @trigger_error(sprintf('The "%s:%s" method is deprecated since league/commonmark 1.4, use "%s" instead.', self::class, 'containsKey()', 'isset($collection[$key])'), E_USER_DEPRECATED);

        return \array_key_exists($key, $this->elements);
    }

    /**
     * Count elements of an object
     *
     * @return int The count as an integer.
     */
    public function count(): int
    {
        return \count($this->elements);
    }

    /**
     * Whether an offset exists
     *
     * @param mixed $offset An offset to check for.
     *
     * @return bool true on success or false on failure.
     */
    public function offsetExists($offset): bool
    {
        return \array_key_exists($offset, $this->elements);
    }

    /**
     * Offset to retrieve
     *
     * @param mixed $offset The offset to retrieve.
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->elements[$offset] ?? null;
    }

    /**
     * Offset to set
     *
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value  The value to set.
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            $this->elements[] = $value;
        } else {
            $this->elements[$offset] = $value;
        }
    }

    /**
     * Offset to unset
     *
     * @param mixed $offset The offset to unset.
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        if (!\array_key_exists($offset, $this->elements)) {
            return;
        }

        unset($this->elements[$offset]);
    }

    /**
     * Returns a subset of the array
     *
     * @param int      $offset
     * @param int|null $length
     *
     * @return array
     */
    public function slice(int $offset, ?int $length = null): array
    {
        return \array_slice($this->elements, $offset, $length, true);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->elements;
    }

    /**
     * @param array $elements
     *
     * @return $this
     *
     * @deprecated
     */
    public function replaceWith(array $elements)
    {
        @trigger_error(sprintf('The "%s:%s" method is deprecated since league/commonmark 1.4.', self::class, 'replaceWith()'), E_USER_DEPRECATED);

        $this->elements = $elements;

        return $this;
    }

    /**
     * @deprecated
     */
    public function removeGaps()
    {
        @trigger_error(sprintf('The "%s:%s" method is deprecated since league/commonmark 1.4.', self::class, 'removeGaps()'), E_USER_DEPRECATED);

        $this->elements = \array_filter($this->elements);
    }
}
