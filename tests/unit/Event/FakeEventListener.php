<?php

/**
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Event;

use League\CommonMark\EnvironmentAwareInterface;
use League\CommonMark\EnvironmentInterface;
use League\CommonMark\Event\AbstractEvent;
use League\CommonMark\Util\ConfigurationAwareInterface;
use League\CommonMark\Util\ConfigurationInterface;

class FakeEventListener implements ConfigurationAwareInterface, EnvironmentAwareInterface
{
    private $callback;
    private $configuration;
    private $environment;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnvironment(EnvironmentInterface $environment)
    {
        $this->environment = $environment;
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }

    public function getEnvironment()
    {
        return $this->environment;
    }

    public function doStuff(AbstractEvent $event)
    {
        return call_user_func($this->callback, $event);
    }
}
