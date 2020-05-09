<?php

declare(strict_types=1);

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
 *
 * @phpstan-template TKey
 * @phpstan-template TValue
 * @phpstan-implements \IteratorAggregate<TKey, TValue>
 * @phpstan-implements \ArrayAccess<TKey, TValue>
 */
class ArrayCollection implements \IteratorAggregate, \Countable, \ArrayAccess
{
    /**
     * @var array<int|string, mixed>
     *
     * @phpstan-var array<TKey, TValue>
     */
    private $elements;

    /**
     * Constructor
     *
     * @param array<int|string, mixed> $elements
     *
     * @phpstan-param array<TKey, TValue> $elements
     */
    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    /**
     * @return mixed|false
     *
     * @phpstan-return TValue|false
     */
    public function first()
    {
        return \reset($this->elements);
    }

    /**
     * @return mixed|false
     *
     * @phpstan-return TValue|false
     */
    public function last()
    {
        return \end($this->elements);
    }

    /**
     * Retrieve an external iterator
     *
     * @return \ArrayIterator<int|string, mixed>
     *
     * @phpstan-return \ArrayIterator<TKey, TValue>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->elements);
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
     * @param int|string $offset An offset to check for.
     *
     * @return bool true on success or false on failure.
     *
     * @phpstan-param TKey $offset
     */
    public function offsetExists($offset): bool
    {
        return \array_key_exists($offset, $this->elements);
    }

    /**
     * Offset to retrieve
     *
     * @param int|string $offset
     *
     * @return mixed|null
     *
     * @phpstan-param TKey $offset
     *
     * @phpstan-return TValue|null
     */
    public function offsetGet($offset)
    {
        return $this->elements[$offset] ?? null;
    }

    /**
     * Offset to set
     *
     * @param int|string|null $offset The offset to assign the value to.
     * @param mixed           $value  The value to set.
     *
     * @phpstan-param TKey|null $offset
     * @phpstan-param TValue    $value
     */
    public function offsetSet($offset, $value): void
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
     * @param int|string $offset The offset to unset.
     *
     * @phpstan-param TKey $offset
     */
    public function offsetUnset($offset): void
    {
        if (! \array_key_exists($offset, $this->elements)) {
            return;
        }

        unset($this->elements[$offset]);
    }

    /**
     * Returns a subset of the array
     *
     * @return array<int|string, mixed>
     *
     * @phpstan-return array<TKey, TValue>
     */
    public function slice(int $offset, ?int $length = null): array
    {
        return \array_slice($this->elements, $offset, $length, true);
    }

    /**
     * @return array<int|string, mixed>
     *
     * @phpstan-return array<TKey, TValue>
     */
    public function toArray(): array
    {
        return $this->elements;
    }
}
