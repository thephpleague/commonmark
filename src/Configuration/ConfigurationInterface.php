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
     * @param string $key Configuration option path/key
     *
     * @return mixed
     *
     * @throws InvalidConfigurationException
     */
    public function get(string $key);

    public function exists(string $key): bool;
}
