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

final class ReadOnlyConfiguration implements ConfigurationInterface
{
    /** @var Configuration */
    private $config;

    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $key)
    {
        return $this->config->get($key);
    }
}
