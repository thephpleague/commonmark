<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Util;

class Configuration
{
    protected $config;

    /**
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        $this->config = $config;
    }

    /**
     * @param array $config
     */
    public function mergeConfig(array $config = array())
    {
        $this->config = array_replace_recursive($this->config, $config);
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config = array())
    {
        $this->config = $config;
    }

    /**
     * @param string|null $key
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    public function getConfig($key = null, $default = null)
    {
        // accept a/b/c as ['a']['b']['c']
        if (strpos($key, '/')) {
            $keyArr = explode('/', $key);
            $data = $this->config;
            foreach ($keyArr as $k) {
                if (!is_array($data) || !isset($data[$k])) {
                    return $default;
                }

                $data = $data[$k];
            }

            return $data;
        }

        if ($key === null) {
            return $this->config;
        }

        if (!isset($this->config[$key])) {
            return $default;
        }

        return $this->config[$key];
    }
}
