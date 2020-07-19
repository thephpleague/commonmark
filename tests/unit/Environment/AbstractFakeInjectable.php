<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\CommonMark\Tests\Unit\Environment;

use League\CommonMark\EnvironmentAwareInterface;
use League\CommonMark\EnvironmentInterface;
use League\CommonMark\Util\ConfigurationAwareInterface;
use League\CommonMark\Util\ConfigurationInterface;

abstract class AbstractFakeInjectable implements ConfigurationAwareInterface, EnvironmentAwareInterface
{
    /** @var ConfigurationInterface|null */
    private $config;

    /** @var EnvironmentInterface|null */
    private $environment;

    public function setConfiguration(ConfigurationInterface $configuration)
    {
        $this->config = $configuration;
    }

    public function setEnvironment(EnvironmentInterface $environment)
    {
        $this->environment = $environment;
    }

    public function getConfig(): ?ConfigurationInterface
    {
        return $this->config;
    }

    public function getEnvironment(): ?EnvironmentInterface
    {
        return $this->environment;
    }
}
