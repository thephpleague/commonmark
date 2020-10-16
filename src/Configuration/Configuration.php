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

use Dflydev\DotAccessData\Data;
use Dflydev\DotAccessData\Exception\InvalidPathException;
use Dflydev\DotAccessData\Exception\MissingPathException;

final class Configuration implements ConfigurationInterface
{
    /**
     * @var Data
     *
     * @psalm-readonly
     */
    private $userConfig;


    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config = [])
    {
        $this->userConfig = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(array $config = []): void
    {
        $this->userConfig->import($config, Data::REPLACE);
    }

    /**
     * {@inheritdoc}
     */
    public function replace(array $config = []): void
    {
        $this->userConfig = new Data($config);
    }

    /**
     * {@inheritDoc}
     */
    public function get(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->userConfig->export();
        }

        try {
            return $this->userConfig->get($key);
        } catch (InvalidPathException | MissingPathException $ex) {
            return $default;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $key, $value = null): void
    {
        $this->userConfig->set($key, $value);
    }
}
