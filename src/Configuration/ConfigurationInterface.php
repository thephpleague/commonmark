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

namespace League\CommonMark\Configuration;

interface ConfigurationInterface
{
    /**
     * Merge an existing array into the current configuration
     *
     * @param array<string, mixed> $config
     */
    public function merge(array $config = []): void;

    /**
     * Replace the entire array with something else
     *
     * @param array<string, mixed> $config
     */
    public function replace(array $config = []): void;

    /**
     * Return the configuration value at the given key, or $default if no such config exists
     *
     * The key can be a string or a slash-delimited path to a nested value
     *
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    public function get(string $key, $default = null);

    /**
     * Set the configuration value at the given key
     *
     * The key can be a string or a slash-delimited path to a nested value
     *
     * @param mixed $value
     */
    public function set(string $key, $value): void;

    /**
     * Returns whether a configuration option exists at the given key
     */
    public function exists(string $key): bool;
}
