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

use League\CommonMark\Exception\InvalidConfigurationException;

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
     * Return the configuration value at the given key
     *
     * @param ?string $key Configuration option path/key
     *
     * @return mixed
     *
     * @throws InvalidConfigurationException if the key does not exist
     */
    public function get(?string $key = null);

    /**
     * Set the configuration value at the given key
     *
     * The key can be a string or a slash-delimited path to a nested value
     *
     * @param mixed|null $value
     */
    public function set(string $key, $value = null): void;
}
