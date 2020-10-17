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
use League\CommonMark\Exception\InvalidConfigurationException;
use Nette\Schema\Elements\Structure;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Schema\Schema;
use Nette\Schema\ValidationException;

final class Configuration implements ConfigurationBuilderInterface, ConfigurationInterface
{
    /**
     * @var Data
     *
     * @psalm-readonly
     */
    private $userConfig;

    /** @var array<string, Schema> */
    private $configSchemas = [];

    /** @var Data|null */
    private $finalConfig;

    /** @var array<string, mixed> */
    private $cache = [];

    /**
     * @var ConfigurationInterface
     *
     * @psalm-readonly
     */
    private $reader;

    /**
     * @param array<string, Schema> $baseSchemas
     */
    public function __construct(array $baseSchemas = [])
    {
        $this->configSchemas = $baseSchemas;
        $this->userConfig    = new Data();

        $this->reader = new ReadOnlyConfiguration($this);
    }

    /**
     * Registers a new configuration schema at the given top-level key
     */
    public function addSchema(string $key, Schema $schema): void
    {
        $this->invalidate();

        if ($schema instanceof Structure) {
            $schema->castTo('array');
        }

        $this->configSchemas[$key] = $schema;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(array $config = []): void
    {
        $this->invalidate();

        $this->userConfig->import($config, Data::REPLACE);
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $key, $value = null): void
    {
        $this->invalidate();

        $this->userConfig->set($key, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $key)
    {
        if ($this->finalConfig === null) {
            $this->finalConfig = $this->build();
        } elseif (\array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }

        try {
            return $this->cache[$key] = $this->finalConfig->get($key);
        } catch (InvalidPathException | MissingPathException $ex) {
            throw InvalidConfigurationException::missingOption($key);
        }
    }

    public function reader(): ConfigurationInterface
    {
        return $this->reader;
    }

    private function invalidate(): void
    {
        $this->cache       = [];
        $this->finalConfig = null;
    }

    /**
     * Applies the schema against the configuration to return the final configuration
     */
    private function build(): Data
    {
        try {
            $schema    = Expect::structure($this->configSchemas)->castTo('array');
            $processor = new Processor();
            $config    = new Data($processor->process($schema, $this->userConfig->export()));

            return $this->finalConfig = $config;
        } catch (ValidationException $ex) {
            throw InvalidConfigurationException::fromValidation($ex);
        }
    }
}
