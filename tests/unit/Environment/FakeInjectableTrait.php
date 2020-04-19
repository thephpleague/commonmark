<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Environment;

use League\CommonMark\Configuration\ConfigurationInterface;
use League\CommonMark\Environment\EnvironmentInterface;

trait FakeInjectableTrait
{
    protected $configInjected = false;
    protected $environmentInjected = false;

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->configInjected = true;
    }

    public function setEnvironment(EnvironmentInterface $environment): void
    {
        $this->environmentInjected = true;
    }

    public function bothWereInjected(): bool
    {
        return $this->configInjected && $this->environmentInjected;
    }
}
